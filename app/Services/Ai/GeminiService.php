<?php

namespace App\Services\Ai;

use App\Models\AiUsage;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    public function quotaStatus(User $user): array
    {
        $limit = (int) config('services.gemini.daily_limit', 30);
        $used = AiUsage::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => max(0, $limit - $used),
        ];
    }

    public function polish(User $user, string $raw, string $style = 'formal'): string
    {
        $raw = mb_substr(trim($raw), 0, 2000);
        if ($raw === '') {
            return '';
        }

        $cfg = config('services.gemini');
        if (! empty($cfg['key'])) {
            try {
                $result = $this->callGemini(
                    [['text' => $raw]],
                    PromptBuilder::polishSystem($style)
                );
                $this->recordUsage($user, 'polish', 'success');

                return trim($result);
            } catch (\Throwable $e) {
                $this->recordUsage($user, 'polish', 'fallback');
            }
        } else {
            $this->recordUsage($user, 'polish', 'local_assist');
        }

        return $this->localPolish($raw, $style);
    }

    public function summarizeEntries(User $user, Collection $entries, string $periodLabel): array
    {
        $lines = $entries->map(fn ($e) => "- [{$e->date->format('d/m/Y')}] ({$e->category->label()}) {$e->title}: {$e->final_description} | Output: {$e->output}")->implode("\n");

        $cfg = config('services.gemini');
        if (! empty($cfg['key']) && $entries->isNotEmpty()) {
            try {
                $rawJson = $this->callGemini(
                    [['text' => PromptBuilder::summaryInstruction($periodLabel) . "\n\n" . $lines]],
                    null,
                    true
                );
                $decoded = json_decode($rawJson, true);
                if (is_array($decoded) && isset($decoded['overview'])) {
                    $this->recordUsage($user, 'summary', 'success');

                    return $decoded;
                }
            } catch (\Throwable $e) {
                $this->recordUsage($user, 'summary', 'fallback');
            }
        } else {
            $this->recordUsage($user, 'summary', 'local_assist');
        }

        $totalHours = round($entries->sum('duration_minutes') / 60, 1);
        $categories = $entries->groupBy(fn ($e) => $e->category->label())->map->count();
        $catSummary = $categories->map(fn ($c, $k) => "{$k} ({$c} entri)")->values()->all();

        return [
            'overview' => "Selama periode {$periodLabel}, peserta telah menyelesaikan {$entries->count()} kegiatan magang dengan total durasi {$totalHours} jam kerja efektif yang mencakup berbagai aktivitas teknis dan kolaborasi tim.",
            'main_activities' => $entries->take(5)->pluck('title')->values()->all(),
            'outputs' => $entries->pluck('output')->filter()->take(4)->values()->all(),
            'obstacles' => $entries->pluck('obstacles')->filter()->take(3)->values()->all(),
            'skills' => array_merge(['Manajemen Waktu', 'Kolaborasi Tim', 'Dokumentasi Teknis'], $catSummary),
        ];
    }

    /**
     * Rule-based weak entry detector (docs/06-ai-gemini-integration.md).
     */
    public function evaluateEntryQuality(string $text, ?string $output = null): array
    {
        $trimmed = trim($text);
        $len = mb_strlen($trimmed);
        $issues = [];
        $score = 100;

        if ($len < 25) {
            $issues[] = 'Deskripsi terlalu singkat (< 25 karakter). Jelaskan langkah yang dikerjakan.';
            $score -= 45;
        } elseif ($len < 60) {
            $issues[] = 'Tambahkan konteks atau metode kerja agar laporan lebih informatif.';
            $score -= 20;
        }

        if (! preg_match('/\b(menyusun|menganalisis|membuat|mengembangkan|menghadiri|melakukan|merapikan|menguji|memperbaiki|mendiskusikan|mempelajari|mengintegrasikan|merancang)\b/i', $trimmed)) {
            $issues[] = 'Gunakan kata kerja aktif (mis. Menyusun, Mengembangkan, Menganalisis).';
            $score -= 15;
        }

        if (empty(trim((string) $output))) {
            $issues[] = 'Kolom Hasil/Output belum diisi (contoh: dokumen, modul, atau catatan rapat).';
            $score -= 15;
        }

        return [
            'score' => max(10, $score),
            'status' => $score >= 80 ? 'Sangat Baik' : ($score >= 60 ? 'Cukup Baik' : 'Perlu Ditingkatkan'),
            'issues' => $issues,
        ];
    }

    protected function callGemini(array $parts, ?string $system = null, bool $json = false, float $temp = 0.3): string
    {
        $cfg = config('services.gemini');
        $url = "{$cfg['base']}/models/{$cfg['model']}:generateContent";

        $payload = [
            'contents' => [['role' => 'user', 'parts' => $parts]],
            'generationConfig' => [
                'temperature' => $temp,
                'maxOutputTokens' => 1024,
            ],
        ];

        if ($system) {
            $payload['systemInstruction'] = ['parts' => [['text' => $system]]];
        }
        if ($json) {
            $payload['generationConfig']['responseMimeType'] = 'application/json';
        }

        $res = Http::withHeaders(['x-goog-api-key' => $cfg['key']])
            ->timeout($cfg['timeout'])
            ->retry(2, 500, throw: false)
            ->post($url, $payload);

        if ($res->failed()) {
            throw new \RuntimeException('Gemini error: ' . $res->status());
        }

        return $res->json('candidates.0.content.parts.0.text') ?? '';
    }

    protected function localPolish(string $raw, string $style): string
    {
        $clean = preg_replace('/\s+/', ' ', trim($raw));
        $replacements = [
            '/\bbikin\b/i' => 'menyusun',
            '/\bbuat\b/i' => 'membuat',
            '/\brapihin\b/i' => 'merapikan',
            '/\bcek\b/i' => 'memeriksa',
            '/\bmeeting\b/i' => 'mengikuti rapat koordinasi terkait',
            '/\bama\b/i' => 'bersama',
            '/\bsama\b/i' => 'bersama',
            '/\bnyoba\b/i' => 'melakukan pengujian pada',
            '/\bfix\b/i' => 'memperbaiki kendala pada',
            '/\bupdate\b/i' => 'memperbarui',
        ];

        $polished = preg_replace(array_keys($replacements), array_values($replacements), $clean);
        $polished = ucfirst(rtrim($polished, '.'));

        if (! preg_match('/^(Menyusun|Membuat|Merapikan|Memeriksa|Mengikuti|Melakukan|Memperbaiki|Memperbarui|Menganalisis|Mengembangkan)/i', $polished)) {
            $polished = 'Melaksanakan kegiatan ' . lcfirst($polished);
        }

        return match ($style) {
            'singkat' => $polished . '.',
            'detail' => $polished . ' secara terstruktur sesuai arahan pembimbing lapangan. Kegiatan ini dilaksanakan untuk memastikan target harian divisi tercapai dengan dokumentasi yang lengkap dan terukur.',
            default => $polished . ' sesuai standar operasional yang berlaku pada divisi penempatan magang.',
        };
    }

    protected function recordUsage(User $user, string $feature, string $status): void
    {
        AiUsage::create([
            'user_id' => $user->id,
            'feature' => $feature,
            'model' => config('services.gemini.model', 'gemini-flash-latest'),
            'input_tokens' => 65,
            'output_tokens' => 110,
            'status' => $status,
            'created_at' => now(),
        ]);
    }
}
