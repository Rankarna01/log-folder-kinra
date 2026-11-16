<?php

namespace App\Http\Controllers;

use App\Enums\LogCategory;
use App\Enums\LogStatus;
use App\Models\Internship;
use App\Models\Logbook;
use App\Services\Ai\GeminiService;
use App\Services\AttachmentService;
use App\Services\LogbookService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function __construct(
        protected LogbookService $logbookService,
        protected AttachmentService $attachmentService,
        protected GeminiService $geminiService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $internship = $user?->activeInternship ?? Internship::with(['participant', 'mentor', 'institution'])->first();

        $monthParam = $request->query('month', now()->timezone('Asia/Jakarta')->format('Y-m'));
        try {
            $currentMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        } catch (\Throwable $e) {
            $currentMonth = Carbon::now('Asia/Jakarta')->startOfMonth();
        }

        $selectedDate = $request->query('date', now()->timezone('Asia/Jakarta')->format('Y-m-d'));

        $query = $internship
            ? $internship->logbooks()->with(['attachments', 'comments.user', 'reviewer'])
            : Logbook::with(['attachments', 'comments.user', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }
        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(fn ($sub) => $sub->where('title', 'like', "%{$q}%")->orWhere('final_description', 'like', "%{$q}%"));
        }

        $logbooks = $query->orderByDesc('date')->orderByDesc('start_time')->get();

        $monthStart = $currentMonth->copy()->startOfMonth();
        $monthEnd = $currentMonth->copy()->endOfMonth();
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $filledDates = $logbooks
            ->map(fn ($l) => $l->date->format('Y-m-d'))
            ->unique()
            ->flip()
            ->all();

        $calendarDays = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $dateStr = $cursor->format('Y-m-d');
            $calendarDays[] = [
                'date' => $dateStr,
                'day' => $cursor->day,
                'in_month' => $cursor->month === $currentMonth->month,
                'is_selected' => $dateStr === $selectedDate,
                'is_filled' => isset($filledDates[$dateStr]),
            ];
            $cursor->addDay();
        }

        $selectedDayLogs = $logbooks->filter(fn ($l) => $l->date->format('Y-m-d') === $selectedDate)->values();
        if ($selectedDayLogs->isEmpty() && $logbooks->isNotEmpty()) {
            $selectedDayLogs = $logbooks->take(2)->values();
        }

        return view('logbook.index', compact(
            'internship',
            'logbooks',
            'currentMonth',
            'selectedDate',
            'calendarDays',
            'selectedDayLogs'
        ));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $internship = $user?->activeInternship ?? Internship::first();
        $templates = $user ? $user->activityTemplates()->latest()->get() : collect();
        $quota = $user ? $this->geminiService->quotaStatus($user) : ['used' => 0, 'limit' => 30, 'remaining' => 30];
        $categories = LogCategory::cases();

        return view('logbook.create', compact('internship', 'templates', 'quota', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string'],
            'raw_description' => ['required', 'string'],
            'ai_description' => ['nullable', 'string'],
            'final_description' => ['nullable', 'string'],
            'output' => ['nullable', 'string'],
            'obstacles' => ['nullable', 'string'],
            'work_location' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'client_uuid' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photo_caption' => ['nullable', 'string', 'max:200'],
        ]);

        $internship = $request->user()?->activeInternship ?? Internship::firstOrFail();

        if (empty($validated['final_description'])) {
            $validated['final_description'] = ($validated['ai_description'] ?? null) ?: $validated['raw_description'];
        }

        $logbook = $this->logbookService->store($internship, $validated);

        if ($request->hasFile('photo')) {
            $this->attachmentService->storeUploadedPhoto(
                $logbook,
                $request->file('photo'),
                $validated['photo_caption'] ?? $logbook->title
            );
        }

        return redirect()
            ->route('logbook.show', $logbook)
            ->with('toast', 'Entri logbook berhasil disimpan!');
    }

    public function show(Logbook $logbook): View
    {
        $logbook->load(['internship.participant', 'internship.mentor', 'internship.institution', 'attachments', 'comments.user', 'reviewer']);
        $quality = $this->geminiService->evaluateEntryQuality($logbook->final_description, $logbook->output);

        return view('logbook.show', compact('logbook', 'quality'));
    }

    public function edit(Logbook $logbook): View
    {
        $categories = LogCategory::cases();
        $quota = auth()->user() ? $this->geminiService->quotaStatus(auth()->user()) : ['used' => 0, 'limit' => 30, 'remaining' => 30];

        return view('logbook.edit', compact('logbook', 'categories', 'quota'));
    }

    public function update(Request $request, Logbook $logbook): RedirectResponse
    {
        abort_unless($logbook->isEditable(), 403, 'Logbook yang sudah disetujui tidak dapat diubah.');

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string'],
            'raw_description' => ['required', 'string'],
            'ai_description' => ['nullable', 'string'],
            'final_description' => ['nullable', 'string'],
            'output' => ['nullable', 'string'],
            'obstacles' => ['nullable', 'string'],
            'work_location' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ]);

        if (empty($validated['final_description'])) {
            $validated['final_description'] = ($validated['ai_description'] ?? null) ?: $validated['raw_description'];
        }

        $this->logbookService->update($logbook, $validated);

        return redirect()
            ->route('logbook.show', $logbook)
            ->with('toast', 'Perubahan logbook berhasil disimpan.');
    }

    public function submit(Logbook $logbook): RedirectResponse
    {
        $logbook->update([
            'status' => LogStatus::Submitted,
            'submitted_at' => now(),
        ]);

        return back()->with('toast', 'Logbook telah dikirim ke Mentor untuk direview.');
    }

    public function duplicate(Logbook $logbook): RedirectResponse
    {
        $copy = $this->logbookService->store($logbook->internship, [
            'date' => today()->toDateString(),
            'start_time' => $logbook->start_time,
            'end_time' => $logbook->end_time,
            'title' => $logbook->title . ' (Lanjutan)',
            'category' => $logbook->category->value,
            'raw_description' => $logbook->raw_description,
            'ai_description' => $logbook->ai_description,
            'final_description' => $logbook->final_description,
            'output' => $logbook->output,
            'work_location' => $logbook->work_location,
            'status' => LogStatus::Draft->value,
        ]);

        return redirect()->route('logbook.show', $copy)->with('toast', 'Entri berhasil diduplikasi untuk hari ini.');
    }

    public function destroy(Logbook $logbook): RedirectResponse
    {
        abort_unless($logbook->isEditable(), 403, 'Hanya logbook berstatus Draft atau Revisi yang dapat dihapus.');
        $logbook->delete();

        return redirect()->route('logbook.index')->with('toast', 'Entri logbook berhasil dihapus.');
    }
}
