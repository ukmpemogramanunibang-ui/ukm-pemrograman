<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * RoomBookingClassifier
 * ---------------------
 * Bertanggung jawab mengklasifikasikan booking ke dalam:
 *  - priority  : red | yellow | blue | green
 *  - activity  : label jenis kegiatan
 *  - roomState : good | damaged | renovation | maintenance | unavailable
 *  - meta      : start, end, status, dsb.
 *
 * Semua logika "kecerdasan" dipindahkan dari Blade ke sini.
 */
class RoomBookingClassifier
{
    /* ============================================================
       KONFIGURASI RULE
       ============================================================ */

    private array $importantKeywords = [
        'sidang', 'ujian', 'akreditasi', 'wisuda', 'rektor', 'pimpinan',
        'kunjungan resmi', 'pelantikan', 'agenda resmi', 'darurat',
        'urgent', 'critical', 'agenda penting',
    ];

    private array $orgKeywords = [
        'rapat', 'organisasi', 'ormawa', 'ukm', 'bem', 'hima',
        'kepanitiaan', 'skripsi', 'tugas akhir', 'makalah', 'seminar',
        'penelitian', 'bimbingan', 'presentasi',
    ];

    private array $classKeywords = [
        'kelas', 'perkuliahan', 'kuliah', 'jadwal belajar',
        'pembelajaran', 'praktikum', 'lab class', 'mata kuliah',
    ];

    private array $statusMap = [
        'approved'            => 'Disetujui',
        'approve'             => 'Disetujui',
        'approved_by_owner'   => 'Disetujui',
        'disetujui'           => 'Disetujui',
        'setuju'              => 'Disetujui',
        'pending'             => 'Menunggu',
        'pending_approval'    => 'Menunggu',
        'menunggu'            => 'Menunggu',
        'menunggu persetujuan'=> 'Menunggu',
        'rejected'            => 'Ditolak',
        'ditolak'             => 'Ditolak',
        'cancelled'           => 'Dibatalkan',
        'dibatalkan'          => 'Dibatalkan',
        'completed'           => 'Selesai',
        'selesai'             => 'Selesai',
    ];

    private array $terminalStatuses = ['rejected', 'ditolak', 'cancelled', 'dibatalkan'];

    /* ============================================================
       PUBLIC API
       ============================================================ */

    /**
     * Klasifikasikan satu booking. Mengembalikan array metadata.
     */
    public function classify($booking): array
    {
        $title    = $this->normalize($this->first($booking, ['title','name','event_name','activity_name','purpose'], 'Booking Ruangan'));
        $purpose  = $this->normalize($this->first($booking, ['purpose','activity_type','type','category','booking_type'], ''));
        $status   = $this->normalize($this->first($booking, ['status','booking_status'], 'pending'));
        $priority = $this->normalize($this->first($booking, ['priority','priority_level','urgency','level'], ''));
        $approval = $this->normalize($this->first($booking, ['approval_status','verification_status'], ''));

        $text = trim("{$title} {$purpose} {$priority}");

        $isImportant = in_array($priority, ['critical','urgent','high','tinggi','prioritas','prioritas tinggi'], true)
            || $this->matchesAny($text, $this->importantKeywords);

        $isOrg = $this->matchesAny($text, $this->orgKeywords);
        $isClass = $this->matchesAny($text, $this->classKeywords)
            || (bool) $this->first($booking, ['is_class','is_scheduled_class','according_to_schedule'], false);

        if ($isImportant) {
            $priorityKey = 'red';
            $priorityLabel = 'Prioritas Tinggi';
            $priorityIcon = 'fa-triangle-exclamation';
            $priorityTone = 'danger';
        } elseif ($isOrg) {
            $priorityKey = 'yellow';
            $priorityLabel = 'Akademik / Organisasi';
            $priorityIcon = 'fa-people-group';
            $priorityTone = 'warning';
        } elseif ($isClass) {
            $priorityKey = 'blue';
            $priorityLabel = 'Kelas / Pembelajaran';
            $priorityIcon = 'fa-book-open';
            $priorityTone = 'info';
        } else {
            $priorityKey = 'green';
            $priorityLabel = 'Kegiatan Umum';
            $priorityIcon = 'fa-calendar-check';
            $priorityTone = 'success';
        }

        // Activity label
        $activityLabel = $this->first($booking, ['activity_type_label','activity_type','type','category','booking_type']);
        if (!$activityLabel) {
            if ($isClass)                            $activityLabel = 'Kelas';
            elseif (preg_match('/\bskripsi|tugas akhir\b/i', $text)) $activityLabel = 'Skripsi';
            elseif (preg_match('/\bmakalah|tugas\b/i', $text))       $activityLabel = 'Makalah / Tugas';
            elseif (preg_match('/\bseminar\b/i', $text))             $activityLabel = 'Seminar';
            elseif (preg_match('/\brapat|organisasi|ukm|bem|hima\b/i', $text)) $activityLabel = 'Rapat Organisasi';
            else                                                     $activityLabel = 'Kegiatan Umum';
        }

        // Room condition
        $room = data_get($booking, 'room');
        $roomName = data_get($room, 'name') ?? data_get($booking, 'room_name') ?? '-';
        $roomCondRaw = $this->normalize($this->first($room, ['condition','room_condition','status','condition_status'], 'baik'));
        [$roomClass, $roomLabel, $roomIcon] = $this->classifyRoomCondition($roomCondRaw);

        // Time
        $start = $this->parseDate($this->first($booking, ['start_datetime','start_at','starts_at']));
        $end   = $this->parseDate($this->first($booking, ['end_datetime','end_at','ends_at']));

        return [
            'id'             => data_get($booking, 'id'),
            'raw'            => $booking,
            'title'          => $this->first($booking, ['title','name','event_name','activity_name'], 'Booking Ruangan'),
            'priority'       => $priorityKey,
            'priority_label' => $priorityLabel,
            'priority_icon'  => $priorityIcon,
            'priority_tone'  => $priorityTone,
            'status_raw'     => $status,
            'status_label'   => $this->statusMap[$status] ?? ucfirst(str_replace('_',' ', $status)),
            'approval_raw'   => $approval,
            'activity_label' => $activityLabel,
            'is_class'       => $isClass,
            'is_important'   => $isImportant,
            'room_id'        => data_get($booking, 'room_id') ?? data_get($room, 'id'),
            'room_name'      => $roomName,
            'room_class'     => $roomClass,
            'room_label'     => $roomLabel,
            'room_icon'      => $roomIcon,
            'start'          => $start,
            'end'            => $end,
        ];
    }

    /**
     * Klasifikasi koleksi booking sekali, kembalikan array keyed by booking id.
     */
    public function classifyMany(Collection $bookings): array
    {
        $result = [];
        foreach ($bookings as $b) {
            $meta = $this->classify($b);
            $key = $meta['id'] ?? spl_object_id($b);
            $result[$key] = $meta;
        }
        return $result;
    }

    /**
     * Deteksi konflik (ruangan + waktu tumpang tindih).
     * Mengembalikan array berisi booking-id yang terindikasi konflik.
     */
    public function detectConflicts(Collection $bookings): array
    {
        $conflicts = [];
        $count = $bookings->count();
        $list  = $bookings->values();

        for ($i = 0; $i < $count; $i++) {
            $a = $list[$i];
            $ma = $this->classify($a);
            if (!$ma['start'] || !$ma['end'] || !$ma['room_id']) continue;
            if (in_array($ma['status_raw'], $this->terminalStatuses, true)) continue;

            for ($j = $i + 1; $j < $count; $j++) {
                $b = $list[$j];
                $mb = $this->classify($b);
                if (!$mb['start'] || !$mb['end'] || !$mb['room_id']) continue;
                if ((string) $ma['room_id'] !== (string) $mb['room_id']) continue;
                if (in_array($mb['status_raw'], $this->terminalStatuses, true)) continue;

                if ($ma['start']->lt($mb['end']) && $mb['start']->lt($ma['end'])) {
                    $ka = (string) ($ma['id'] ?? spl_object_id($a));
                    $kb = (string) ($mb['id'] ?? spl_object_id($b));
                    $conflicts[$ka] = true;
                    $conflicts[$kb] = true;
                }
            }
        }

        return array_keys($conflicts);
    }

    /* ============================================================
       HELPERS
       ============================================================ */

    private function classifyRoomCondition(string $raw): array
    {
        if (preg_match('/\b(renovasi|renovation)\b/i', $raw))
            return ['renovation', 'Renovasi', 'fa-hammer'];
        if (preg_match('/\b(rusak|damage|damaged|broken)\b/i', $raw))
            return ['damaged', 'Rusak', 'fa-screwdriver-wrench'];
        if (preg_match('/\b(perawatan|maintenance|servis|service)\b/i', $raw))
            return ['maintenance', 'Perawatan', 'fa-wrench'];
        if (preg_match('/\b(tidak tersedia|unavailable|ditutup|closed)\b/i', $raw))
            return ['unavailable', 'Tidak Tersedia', 'fa-ban'];
        if (preg_match('/\b(bersih|clean)\b/i', $raw))
            return ['good', 'Bersih & Baik', 'fa-circle-check'];
        return ['good', 'Baik', 'fa-circle-check'];
    }

    private function first($model, array $fields, $default = null)
    {
        foreach ($fields as $f) {
            $v = data_get($model, $f);
            if ($v !== null && $v !== '') return $v;
        }
        return $default;
    }

    private function parseDate($value): ?Carbon
    {
        if (!$value) return null;
        try { return Carbon::parse($value); } catch (\Throwable) { return null; }
    }

    private function normalize($value): string
    {
        return strtolower(trim((string) $value));
    }

    private function matchesAny(string $text, array $keywords): bool
    {
        if ($text === '') return false;
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) return true;
        }
        return false;
    }
}