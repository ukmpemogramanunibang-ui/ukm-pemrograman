<?php

namespace App\Helpers;

use App\Models\Notification;

/**
 * NotificationPresenter
 * --------------------
 * Mengubah notifikasi dari format apa pun menjadi tampilan terstandardisasi.
 *
 * Tidak peduli notif dari chat, pengumuman, booking, LMS, atau sistem lain —
 * presenter ini akan ekstrak: title, message, sender, source_label, icon, priority.
 */
class NotificationPresenter
{
    /**
     * Format data notifikasi untuk ditampilkan.
     */
    public static function present(Notification $n): array
    {
        $data = self::decodeData($n->data ?? []);

        return [
            'id'             => $n->id,
            'title'          => self::extractTitle($n, $data),
            'message'        => self::extractMessage($n, $data),
            'sender_name'    => self::extractSenderName($n, $data),
            'source'         => $n->source ?: self::detectSource($n, $data),
            'source_label'   => self::sourceLabel($n, $data),
            'source_emoji'   => self::sourceEmoji($n, $data),
            'priority'       => $n->priority ?: 'normal',
            'is_important'   => self::isImportant($n),
            'is_urgent'      => self::isUrgent($n),
            'is_unread'      => is_null($n->read_at),
            'is_starred'     => ! is_null($n->starred_at),
            'is_archived'    => ! is_null($n->archived_at),
            'requires_action'=> (bool) ($n->requires_action ?? false),
            'actioned_at'    => $n->actioned_at,
            'action_url'     => $n->action_url ?: null,
            'group_names'    => $data['group_names'] ?? [],
            'group_count'    => $data['group_count'] ?? 0,
            'time_ago'       => self::formatTimeAgo($n),
            'time_full'      => $n->created_at?->isoFormat('D MMM Y, HH:mm'),
            'created_at'     => $n->created_at,
        ];
    }

    /* =========================================================
     | EXTRACTION HELPERS
     * ========================================================= */

    protected static function decodeData($data): array
    {
        if (is_array($data)) return $data;
        if (is_string($data)) {
            // Handle double-encoded JSON
            $decoded = json_decode($data, true);
            if (is_string($decoded)) $decoded = json_decode($decoded, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    protected static function extractTitle(Notification $n, array $data): string
    {
        // Prioritas: kolom title → data.title → data.subject → fallback
        if (! empty($n->title) && $n->title !== 'Notifikasi Baru' && $n->title !== 'Notifikasi') {
            return $n->title;
        }

        return $data['title']
            ?? $data['subject']
            ?? $data['announcement_title']
            ?? (isset($data['sender_name']) && isset($data['conversation_id'])
                ? 'Pesan dari ' . $data['sender_name']
                : null)
            ?? (isset($data['sender_id']) && isset($data['message_preview'])
                ? 'Pesan Chat Baru'
                : null)
            ?? 'Notifikasi Baru';
    }

    protected static function extractMessage(Notification $n, array $data): string
    {
        if (! empty($n->message) && $n->message !== 'Data telah diperbarui.') {
            return $n->message;
        }

        return $data['message']
            ?? $data['message_preview']
            ?? $data['body']
            ?? $data['content']
            ?? $data['description']
            ?? $data['notes']
            ?? 'Anda memiliki notifikasi baru. Silakan buka untuk melihat detail.';
    }

    protected static function extractSenderName(Notification $n, array $data): ?string
    {
        if (! empty($data['sender_name'])) return $data['sender_name'];
        if (! empty($data['by']))          return $data['by'];
        if (! empty($data['created_by']))  return $data['created_by'];
        if (! empty($data['actor_name']))  return $data['actor_name'];

        // Fallback: query user
        if (! empty($n->actor_id)) {
            $user = \App\Models\User::find($n->actor_id);
            return $user?->name;
        }
        if (! empty($data['sender_id'])) {
            $user = \App\Models\User::find($data['sender_id']);
            return $user?->name;
        }

        return null;
    }

    protected static function detectSource(Notification $n, array $data): string
    {
        if ($n->source) return $n->source;

        // Deteksi dari tipe notifikasi
        $type = $n->type ?? '';
        if (str_contains($type, 'GroupAnnouncement'))   return 'group_announcement';
        if (str_contains($type, 'Chat'))                return 'chat';
        if (str_contains($type, 'Message'))             return 'chat';
        if (str_contains($type, 'Booking'))             return 'booking';
        if (str_contains($type, 'Attendance'))          return 'attendance';
        if (str_contains($type, 'Lms'))                 return 'lms';
        if (str_contains($type, 'Financial'))           return 'financial';

        // Deteksi dari data
        if (isset($data['conversation_id'])) return 'chat';
        if (isset($data['announcement_id'])) return 'group_announcement';
        if (isset($data['booking_id']))      return 'booking';
        if (isset($data['attendance_id']))   return 'attendance';
        if (isset($data['lms_item_id']))     return 'lms';

        return 'system';
    }

    protected static function sourceLabel(Notification $n, array $data): string
    {
        $source = $n->source ?: self::detectSource($n, $data);
        $meta   = config("unicode_notifications.sources.{$source}", []);

        return $meta['label'] ?? ucfirst(str_replace('_', ' ', $source));
    }

    protected static function sourceEmoji(Notification $n, array $data): string
    {
        $source = $n->source ?: self::detectSource($n, $data);
        $meta   = config("unicode_notifications.sources.{$source}", []);

        return $meta['emoji'] ?? '🔔';
    }

    /* =========================================================
     | PRIORITY HELPERS
     * ========================================================= */

    protected static function isImportant(Notification $n): bool
    {
        $priority = $n->priority ?? 'normal';
        return in_array($priority, ['important', 'high', 'urgent'], true);
    }

    protected static function isUrgent(Notification $n): bool
    {
        return ($n->priority ?? 'normal') === 'urgent';
    }

    /* =========================================================
     | TIME FORMATTING
     * ========================================================= */

    protected static function formatTimeAgo(Notification $n): string
    {
        $time = $n->created_at;
        if (! $time) return '';

        if ($time->isToday()) {
            $diff = $time->diffInMinutes(now());
            if ($diff < 1)    return 'Baru saja';
            if ($diff < 60)   return "{$diff} menit lalu";
            return $time->format('H:i');
        }

        if ($time->isYesterday()) {
            return 'Kemarin, ' . $time->format('H:i');
        }

        if ($time->isCurrentWeek()) {
            return $time->isoFormat('dddd, HH:mm');
        }

        if ($time->isCurrentYear()) {
            return $time->isoFormat('D MMM, HH:mm');
        }

        return $time->isoFormat('D MMM YYYY');
    }
}