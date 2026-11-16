<?php

namespace App\Services;

use App\Models\Report;

class PdfService
{
    public function buildViewData(Report $report): array
    {
        $internship = $report->internship()->with(['participant', 'mentor', 'institution'])->firstOrFail();
        $options = $report->options ?? [];

        $query = $internship->logbooks()
            ->with(['attachments', 'reviewer'])
            ->whereBetween('date', [$report->period_start->toDateString(), $report->period_end->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time');

        if (! empty($options['only_approved'])) {
            $query->where('status', 'approved');
        }

        $entries = $query->get();
        $totalMinutes = $entries->sum('duration_minutes');
        $totalHours = round($totalMinutes / 60, 1);
        $summary = $report->ai_summary ? json_decode($report->ai_summary, true) : null;

        return [
            'report' => $report,
            'internship' => $internship,
            'participant' => $internship->participant,
            'mentor' => $internship->mentor,
            'institution' => $internship->institution,
            'entries' => $entries,
            'totalHours' => $totalHours,
            'totalDays' => $entries->pluck('date')->map->toDateString()->unique()->count(),
            'summary' => $summary,
            'verifyUrl' => url('/verify/' . $report->verify_token),
        ];
    }
}
