<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreSubmissionRequest;
use App\Models\Lms\LmsItem;
use App\Models\Lms\LmsItemUser;
use App\Models\Lms\LmsSubmission;
use App\Services\Lms\LmsAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function __construct(protected LmsAuditService $audit) {}

    public function index(Request $request)
    {
        $q = LmsSubmission::with(['item.group', 'user', 'reviewer'])
            ->latest('submitted_at');

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $submissions = $q->paginate(15);
        return view('owner.lms.submissions.index', compact('submissions'));
    }

    public function pending()
    {
        $submissions = LmsSubmission::with(['item.group', 'user'])
            ->where('status', 'pending_review')
            ->latest('submitted_at')
            ->paginate(15);

        return view('owner.lms.submissions.pending', compact('submissions'));
    }

    public function late()
    {
        $submissions = LmsSubmission::with(['item.group', 'user'])
            ->where('is_late', true)
            ->latest('submitted_at')
            ->paginate(15);

        return view('owner.lms.submissions.late', compact('submissions'));
    }

    public function show(LmsSubmission $submission)
    {
        $submission->load(['item.group', 'user', 'files', 'reviewer']);
        return view('owner.lms.submissions.show', compact('submission'));
    }

    public function review(Request $request, LmsSubmission $submission)
    {
        $request->validate([
            'action'   => ['required', 'in:approve,revision'],
            'score'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $newStatus = $request->action === 'approve' ? 'completed' : 'revision';

        $submission->update([
            'status'      => $newStatus,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'score'       => $request->score,
            'feedback'    => $request->feedback,
        ]);

        // Sync status penerima
        LmsItemUser::where('item_id', $submission->item_id)
            ->where('user_id', $submission->user_id)
            ->update(['status' => $newStatus === 'completed' ? 'completed' : 'revision']);

        $this->audit->log(
            $newStatus === 'completed' ? 'submission_approved' : 'submission_revision',
            $submission,
            ['score' => $request->score, 'feedback' => $request->feedback]
        );

        return back()->with('success', 'Pengumpulan berhasil diproses.');
    }

    public function mySubmissions()
    {
        $submissions = LmsSubmission::with('item.group')
            ->where('user_id', Auth::id())
            ->latest('submitted_at')
            ->paginate(15);

        return view('owner.lms.submissions.my', compact('submissions'));
    }
    // app/Http/Controllers/Owner/Lms/SubmissionController.php (tambahkan method)

public function create(Request $request, LmsItem $item)
{
    $assignment = $request->attributes->get('lms_assignment');
    // $assignment sudah dijamin ada oleh middleware VerifiedRecipient

    // Update viewed_at jika belum
    if (!$assignment->viewed_at) {
        $assignment->update([
            'viewed_at' => now(),
            'status'    => $assignment->status === 'new' ? 'viewed' : $assignment->status,
        ]);
    }

    return view('owner.lms.items.submit', compact('item', 'assignment'));
}

public function store(StoreSubmissionRequest $request, LmsItem $item)
{
    $assignment = $request->attributes->get('lms_assignment');
    $user       = $request->user();

    $isLate = $item->deadline_at && now()->gt($item->deadline_at);

    DB::beginTransaction();
    try {
        // Buat submission
        $submission = LmsSubmission::create([
            'item_id'      => $item->id,
            'user_id'      => $user->id,
            'answer_text'  => $request->input('answer_text'),
            'answer_link'  => $request->input('answer_link'),
            'note'         => $request->input('note'),
            'status'       => $item->requires_review ? 'pending_review' : 'completed',
            'submitted_at' => now(),
            'is_late'      => $isLate,
            'is_resubmit'  => !is_null($assignment->submitted_at),
        ]);

        // Upload files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('lms/submissions/' . $submission->uuid, 'public');
                $submission->files()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'path'          => $path,
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
        }

        // Update assignment
        $assignment->update([
            'submitted_at' => now(),
            'status'       => $isLate ? 'late' : ($item->requires_review ? 'pending_review' : 'completed'),
        ]);

        DB::commit();

        return redirect()
            ->route('owner.lms.items.show', $item->id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    } catch (\Throwable $e) {
        DB::rollBack();
        report($e);
        return back()->with('error', 'Gagal mengumpulkan: ' . $e->getMessage());
    }
}

public function markViewed(Request $request, LmsItem $item)
{
    $assignment = $request->attributes->get('lms_assignment');
    if (!$assignment->viewed_at) {
        $assignment->update([
            'viewed_at' => now(),
            'status'    => $assignment->status === 'new' ? 'viewed' : $assignment->status,
        ]);
    }
    return response()->json(['ok' => true]);
}

public function markStarted(Request $request, LmsItem $item)
{
    $assignment = $request->attributes->get('lms_assignment');
    $assignment->update([
        'started_at' => $assignment->started_at ?? now(),
        'status'     => in_array($assignment->status, ['new', 'viewed']) ? 'in_progress' : $assignment->status,
    ]);
    return response()->json(['ok' => true]);
}
}