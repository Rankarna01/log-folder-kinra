<?php

namespace App\Services;

use App\Models\Internship;
use App\Models\Report;
use App\Services\Ai\GeminiService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReportService
{
    public function __construct(
        protected GeminiService $geminiService
    ) {}

    public function generateReport(Internship $internship, string $type, string $start, string $end, array $options = []): Report
    {
        $periodStart = Carbon::parse($start)->startOfDay();
        $periodEnd = Carbon::parse($end)->endOfDay();

        $query = $internship->logbooks()
            ->with(['attachments', 'reviewer'])
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time');

        if (! empty($options['only_approved'])) {
            $query->where('status', 'approved');
        }

        $entries = $query->get();
        $aiSummary = null;

        if (! empty($options['include_ai_summary'])) {
            $summaryData = $this->geminiService->summarizeEntries(
                $internship->participant,
                $entries,
                $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y')
            );
            $aiSummary = json_encode($summaryData, JSON_UNESCAPED_UNICODE);
        }

        return Report::create([
            'internship_id' => $internship->id,
            'type' => $type,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'options' => $options,
            'ai_summary' => $aiSummary,
            'status' => 'ready',
            'verify_token' => Str::random(32),
            'generated_at' => now(),
        ]);
    }
}
