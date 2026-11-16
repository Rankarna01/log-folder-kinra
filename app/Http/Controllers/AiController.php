<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\User;
use App\Services\Ai\GeminiService;
use App\Services\LogbookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(
        protected GeminiService $geminiService,
        protected LogbookService $logbookService
    ) {}

    public function polish(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:3000'],
            'style' => ['nullable', 'string', 'in:formal,singkat,detail'],
            'output' => ['nullable', 'string'],
        ]);

        $user = $request->user() ?? User::firstOrFail();
        $quota = $this->geminiService->quotaStatus($user);

        if ($quota['remaining'] <= 0) {
            return response()->json([
                'message' => 'Kuota AI harian habis.',
            ], 429);
        }

        $style = $validated['style'] ?? 'formal';
        $result = $this->geminiService->polish($user, $validated['text'], $style);
        $quality = $this->geminiService->evaluateEntryQuality($result, $validated['output'] ?? null);

        return response()->json([
            'result' => $result,
            'quality' => $quality,
            'quota' => $this->geminiService->quotaStatus($user),
        ]);
    }

    public function transcribe(Request $request): JsonResponse
    {
        $user = $request->user() ?? User::firstOrFail();
        $rawVoiceText = $request->input('voice_text', 'hari ini saya bikin desain komponen uiux dashboard dan meeting sama mentor buat review wireframe aplikasi');
        $style = $request->input('style', 'formal');

        $polished = $this->geminiService->polish($user, $rawVoiceText, $style);

        return response()->json([
            'transcript' => $rawVoiceText,
            'polished' => $polished,
            'quota' => $this->geminiService->quotaStatus($user),
        ]);
    }

    public function photoSuggest(Request $request): JsonResponse
    {
        $filename = $request->input('filename', 'dokumentasi-kegiatan.jpg');

        return response()->json([
            'caption' => 'Dokumentasi pengerjaan tugas dan koordinasi teknis pada divisi magang (' . $filename . ').',
            'suggestion' => 'Melaksanakan pengerjaan modul tugas harian serta mendokumentasikan progres bersama tim pembimbing di lapangan.',
        ]);
    }

    public function quota(Request $request): JsonResponse
    {
        $user = $request->user() ?? User::firstOrFail();

        return response()->json($this->geminiService->quotaStatus($user));
    }

    public function syncOffline(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $internship = $request->user()?->activeInternship ?? Internship::firstOrFail();

        $synced = [];
        $failed = [];

        foreach ($items as $item) {
            try {
                $logbook = $this->logbookService->store($internship, [
                    'client_uuid' => $item['client_uuid'] ?? null,
                    'date' => $item['date'] ?? today()->toDateString(),
                    'start_time' => $item['start_time'] ?? '08:30',
                    'end_time' => $item['end_time'] ?? '12:00',
                    'title' => $item['title'] ?? 'Entri Offline',
                    'category' => $item['category'] ?? 'tugas',
                    'raw_description' => $item['raw_description'] ?? '',
                    'final_description' => $item['final_description'] ?? $item['raw_description'] ?? '',
                    'output' => $item['output'] ?? null,
                    'source' => 'offline_sync',
                    'status' => 'draft',
                ]);

                $synced[] = [
                    'client_uuid' => $logbook->client_uuid,
                    'id' => $logbook->id,
                ];
            } catch (\Throwable $e) {
                $failed[] = [
                    'client_uuid' => $item['client_uuid'] ?? null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'synced' => $synced,
            'failed' => $failed,
        ]);
    }
}
