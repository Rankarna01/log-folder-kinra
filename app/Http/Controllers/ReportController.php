<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\Report;
use App\Services\Ai\GeminiService;
use App\Services\PdfService;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected PdfService $pdfService,
        protected GeminiService $geminiService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $internship = $user->activeInternship()->with(['participant', 'mentor', 'institution', 'logbooks'])->first();

        if (! $internship) {
            return redirect()->route('onboarding.show');
        }

        $reports = $internship
            ? $internship->reports()->latest()->get()
            : Report::latest()->get();

        $entries = $internship ? $internship->logbooks()->orderByDesc('date')->get() : collect();
        $aiSummary = $user && $entries->isNotEmpty()
            ? $this->geminiService->summarizeEntries($user, $entries, 'November 2026')
            : null;

        return view('reports.index', compact('internship', 'reports', 'entries', 'aiSummary'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:daily,weekly,monthly,custom,final'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'include_photos' => ['nullable', 'boolean'],
            'include_ai_summary' => ['nullable', 'boolean'],
            'only_approved' => ['nullable', 'boolean'],
        ]);

        $internship = $request->user()?->activeInternship()->first() ?? Internship::firstOrFail();

        $report = $this->reportService->generateReport(
            $internship,
            $validated['type'],
            $validated['period_start'],
            $validated['period_end'],
            [
                'include_photos' => $request->boolean('include_photos', true),
                'include_ai_summary' => $request->boolean('include_ai_summary', true),
                'only_approved' => $request->boolean('only_approved', false),
            ]
        );

        return redirect()
            ->route('reports.download', $report)
            ->with('toast', 'Laporan PDF berhasil digenerate!');
    }

    public function download(Request $request, Report $report)
    {
        $data = $this->pdfService->buildViewData($report);

        if ($request->boolean('raw_pdf') && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.monthly', $data)->setPaper('a4', 'portrait');

            return $pdf->download("Laporan-Magang-{$report->type}-{$report->period_start->format('Ymd')}.pdf");
        }

        return view('pdf.monthly', $data);
    }

    public function verify(string $token): View
    {
        $report = Report::with(['internship.participant', 'internship.mentor', 'internship.institution'])
            ->where('verify_token', $token)
            ->firstOrFail();

        return view('reports.verify', compact('report'));
    }
}
