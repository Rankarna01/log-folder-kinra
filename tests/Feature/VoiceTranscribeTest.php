<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VoiceTranscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_transcribe_returns_service_unavailable_when_gemini_key_is_missing(): void
    {
        config(['services.gemini.key' => null]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/ai/transcribe', [
            'audio' => UploadedFile::fake()->create('rekaman.webm', 10, 'audio/webm'),
        ]);

        $response->assertStatus(503)->assertJsonPath('message', 'Layanan transkripsi suara belum dikonfigurasi. Isi GEMINI_API_KEY di file .env.');
    }

    public function test_transcribe_returns_transcript_from_gemini(): void
    {
        config(['services.gemini.key' => 'test-key']);
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => 'Hari ini saya membuat desain dashboard.']]]]],
            ]),
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/ai/transcribe', [
            'audio' => UploadedFile::fake()->create('rekaman.webm', 10, 'audio/webm;codecs=opus'),
        ]);

        $response->assertOk()
            ->assertJsonPath('transcript', 'Hari ini saya membuat desain dashboard.')
            ->assertJsonPath('quota.used', 1);

        Http::assertSent(function ($request): bool {
            $parts = $request->data()['contents'][0]['parts'];

            return $parts[1]['inline_data']['mime_type'] === 'audio/webm';
        });
    }

    public function test_transcribe_rejects_unsupported_audio_format(): void
    {
        config(['services.gemini.key' => 'test-key']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/ai/transcribe', [
            'audio' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ]);

        $response->assertStatus(422)->assertJsonPath('message', 'Format rekaman tidak didukung.');
    }

    public function test_transcribe_returns_too_many_requests_when_daily_quota_is_used_up(): void
    {
        config([
            'services.gemini.key' => 'test-key',
            'services.gemini.daily_limit' => 0,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/ai/transcribe', [
            'audio' => UploadedFile::fake()->create('rekaman.webm', 10, 'audio/webm'),
        ]);

        $response->assertStatus(429)->assertJsonPath('message', 'Kuota AI harian habis.');
    }

    public function test_transcribe_returns_bad_gateway_when_gemini_fails(): void
    {
        config(['services.gemini.key' => 'test-key']);
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'unavailable'], 500),
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/ai/transcribe', [
            'audio' => UploadedFile::fake()->create('rekaman.webm', 10, 'audio/webm'),
        ]);

        $response->assertStatus(502)->assertJsonPath('message', 'Transkripsi gagal. Coba lagi atau ketik manual.');
    }
}
