<?php

namespace App\Http\Controllers;

use App\Enums\LogStatus;
use App\Models\Comment;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\User;
use App\Services\LogbookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        protected LogbookService $logbookService
    ) {}

    public function index(Request $request): View
    {
        $pendingLogs = Logbook::with(['internship.participant', 'attachments', 'comments.user'])
            ->whereIn('status', [LogStatus::Submitted->value, LogStatus::Revision->value])
            ->orderByDesc('date')
            ->get();

        $approvedLogs = Logbook::with(['internship.participant', 'attachments'])
            ->where('status', LogStatus::Approved->value)
            ->orderByDesc('reviewed_at')
            ->take(10)
            ->get();

        $internships = Internship::with(['participant', 'institution', 'logbooks'])->get();

        return view('mentor.review', compact('pendingLogs', 'approvedLogs', 'internships'));
    }

    public function approve(Request $request, Logbook $logbook): RedirectResponse
    {
        $mentor = $request->user() ?? User::where('role', 'mentor')->firstOrFail();
        $this->logbookService->review($logbook, $mentor, 'approve', $request->input('message'));

        return back()->with('toast', "Logbook \"{$logbook->title}\" berhasil di-approve!");
    }

    public function revision(Request $request, Logbook $logbook): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:3'],
        ]);

        $mentor = $request->user() ?? User::where('role', 'mentor')->firstOrFail();
        $this->logbookService->review($logbook, $mentor, 'revision', $validated['message']);

        return back()->with('toast', 'Permintaan revisi dan catatan mentor telah dikirim ke peserta.');
    }

    public function comment(Request $request, Logbook $logbook): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2'],
        ]);

        $user = $request->user() ?? User::firstOrFail();

        Comment::create([
            'logbook_id' => $logbook->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'type' => 'comment',
        ]);

        return back()->with('toast', 'Komentar diskusi berhasil ditambahkan.');
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $mentor = $request->user() ?? User::where('role', 'mentor')->firstOrFail();
        $count = Logbook::where('status', LogStatus::Submitted->value)->update([
            'status' => LogStatus::Approved->value,
            'reviewed_at' => now(),
            'reviewed_by' => $mentor->id,
        ]);

        return back()->with('toast', "{$count} entri logbook berhasil di-approve sekaligus!");
    }
}
