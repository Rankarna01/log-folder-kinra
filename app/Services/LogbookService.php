<?php

namespace App\Services;

use App\Enums\LogStatus;
use App\Models\Comment;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LogbookService
{
    public function calculateDurationMinutes(string $startTime, string $endTime): int
    {
        $start = Carbon::createFromFormat('H:i', substr($startTime, 0, 5));
        $end = Carbon::createFromFormat('H:i', substr($endTime, 0, 5));

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        return (int) $start->diffInMinutes($end);
    }

    public function store(Internship $internship, array $data): Logbook
    {
        if (! empty($data['client_uuid'])) {
            $existing = Logbook::where('client_uuid', $data['client_uuid'])->first();
            if ($existing) {
                return $existing;
            }
        }

        $duration = $this->calculateDurationMinutes($data['start_time'], $data['end_time']);
        $raw = $data['raw_description'] ?? $data['final_description'] ?? '';
        $final = $data['final_description'] ?? $data['ai_description'] ?? $raw;
        $status = $data['status'] ?? LogStatus::Draft->value;

        return Logbook::create([
            'internship_id' => $internship->id,
            'date' => $data['date'],
            'start_time' => substr($data['start_time'], 0, 5),
            'end_time' => substr($data['end_time'], 0, 5),
            'duration_minutes' => $duration,
            'title' => $data['title'],
            'category' => $data['category'] ?? 'tugas',
            'raw_description' => $raw,
            'ai_description' => $data['ai_description'] ?? null,
            'final_description' => $final,
            'output' => $data['output'] ?? null,
            'obstacles' => $data['obstacles'] ?? null,
            'work_location' => $data['work_location'] ?? 'wfo',
            'status' => $status,
            'submitted_at' => $status === LogStatus::Submitted->value ? now() : null,
            'source' => $data['source'] ?? 'manual',
            'client_uuid' => $data['client_uuid'] ?? (string) Str::uuid(),
        ]);
    }

    public function update(Logbook $logbook, array $data): Logbook
    {
        $duration = $this->calculateDurationMinutes($data['start_time'], $data['end_time']);
        $raw = $data['raw_description'] ?? $logbook->raw_description;
        $final = $data['final_description'] ?? $logbook->final_description;
        $status = $data['status'] ?? $logbook->status->value;

        $logbook->update([
            'date' => $data['date'],
            'start_time' => substr($data['start_time'], 0, 5),
            'end_time' => substr($data['end_time'], 0, 5),
            'duration_minutes' => $duration,
            'title' => $data['title'],
            'category' => $data['category'] ?? $logbook->category->value,
            'raw_description' => $raw,
            'ai_description' => $data['ai_description'] ?? $logbook->ai_description,
            'final_description' => $final,
            'output' => $data['output'] ?? $logbook->output,
            'obstacles' => $data['obstacles'] ?? $logbook->obstacles,
            'work_location' => $data['work_location'] ?? $logbook->work_location,
            'status' => $status,
            'submitted_at' => $status === LogStatus::Submitted->value ? ($logbook->submitted_at ?? now()) : $logbook->submitted_at,
        ]);

        return $logbook->fresh();
    }

    public function review(Logbook $logbook, User $mentor, string $action, ?string $message = null): Logbook
    {
        $newStatus = $action === 'approve' ? LogStatus::Approved : LogStatus::Revision;

        $logbook->update([
            'status' => $newStatus,
            'reviewed_at' => now(),
            'reviewed_by' => $mentor->id,
        ]);

        if (! empty($message) || $action === 'approve') {
            Comment::create([
                'logbook_id' => $logbook->id,
                'user_id' => $mentor->id,
                'message' => $message ?: 'Logbook telah diperiksa dan disetujui.',
                'type' => $action === 'approve' ? 'approval' : 'revision_request',
            ]);
        }

        return $logbook->fresh(['comments.user', 'reviewer']);
    }
}
