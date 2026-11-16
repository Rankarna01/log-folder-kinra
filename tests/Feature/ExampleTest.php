<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\Report;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_dashboard_renders_pastel_bento_ui_and_logbooks(): void
    {
        $participant = User::where('role', 'participant')->firstOrFail();

        $response = $this->actingAs($participant)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('Esther Howard');
        $response->assertSeeText('Mastering projects with management');
        $response->assertSeeText('Website Design & Design System');
    }

    public function test_logbook_calendar_detail_and_creation_flow(): void
    {
        $participant = User::where('role', 'participant')->firstOrFail();

        $calendarRes = $this->actingAs($participant)->get('/logbook?month=2026-11&date=2026-11-10');
        $calendarRes->assertStatus(200);
        $calendarRes->assertSeeText('November 2026');
        $calendarRes->assertSeeText('Delivery App UI Kit');

        $this->withoutExceptionHandling();
        $storeRes = $this->actingAs($participant)
            ->post('/logbook', [
                'date' => '2026-11-16',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'title' => 'Implementasi Komponen Floating Neo-Dock',
                'category' => 'tugas',
                'raw_description' => 'bikin komponen floating dock sesuai style-design.png',
                'output' => 'Floating Dock • Squircle FAB',
                'work_location' => 'wfo',
                'status' => 'submitted',
            ]);

        $storeRes->assertSessionHasNoErrors();
        $newLog = Logbook::where('title', 'Implementasi Komponen Floating Neo-Dock')->firstOrFail();
        $storeRes->assertRedirect(route('logbook.show', $newLog));
        $this->assertEquals(180, $newLog->duration_minutes);

        $detailRes = $this->actingAs($participant)->get(route('logbook.show', $newLog));
        $detailRes->assertStatus(200);
        $detailRes->assertSeeText('Floating Dock');
    }

    public function test_ai_polish_transcribe_and_offline_sync_endpoints(): void
    {
        $participant = User::where('role', 'participant')->firstOrFail();

        $polishRes = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->actingAs($participant)
            ->postJson('/api/ai/polish', [
                'text' => 'bikin laporan bulanan dan rapihin desain komponen',
                'style' => 'formal',
            ]);

        $polishRes->assertStatus(200)
            ->assertJsonStructure(['result', 'quality', 'quota']);

        $syncRes = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->actingAs($participant)
            ->postJson('/api/logbooks/sync', [
                'items' => [
                    [
                        'client_uuid' => '11111111-2222-3333-4444-555555555555',
                        'date' => '2026-11-16',
                        'start_time' => '13:00',
                        'end_time' => '15:00',
                        'title' => 'Entri Offline PWA',
                        'raw_description' => 'Testing sinkronisasi otomatis dari IndexedDB',
                    ],
                ],
            ]);

        $syncRes->assertStatus(200)
            ->assertJsonCount(1, 'synced');
    }

    public function test_mentor_review_and_pdf_report_verification(): void
    {
        $mentor = User::where('role', 'mentor')->firstOrFail();
        $logbook = Logbook::where('status', 'submitted')->firstOrFail();

        $approveRes = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->actingAs($mentor)
            ->post(route('review.approve', $logbook), [
                'message' => 'Sangat rapi, disetujui.',
            ]);
        $approveRes->assertRedirect();
        $this->assertEquals('approved', $logbook->fresh()->status->value);

        $report = Report::firstOrFail();
        $pdfRes = $this->actingAs($mentor)->get(route('reports.download', $report));
        $pdfRes->assertStatus(200);
        $pdfRes->assertSeeText($report->verify_token);

        $verifyRes = $this->get(route('reports.verify', $report->verify_token));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSeeText('Dokumen Resmi & Terverifikasi');
    }
}
