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

    public function test_participant_registration_redirects_directly_to_create_logbook(): void
    {
        $registerPage = $this->get('/register');
        $registerPage->assertStatus(200);
        $registerPage->assertSeeText('Registrasi Peserta Magang');

        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'division' => 'IT & Digital Product',
            'position' => 'Fullstack Web Developer Intern',
            'mentor_name' => 'Hendra Wijaya',
        ]);

        $response->assertRedirect(route('logbook.create'));
        $this->assertAuthenticated();
        $this->assertEquals('Budi Santoso', auth()->user()->name);
        $this->assertEquals('IT & Digital Product', auth()->user()->activeInternship->division);
        $this->assertEquals('Fullstack Web Developer Intern', auth()->user()->activeInternship->position);
        $this->assertEquals('Hendra Wijaya', auth()->user()->activeInternship->mentor->name);

        $createPage = $this->get(route('logbook.create'));
        $createPage->assertStatus(200);
        $createPage->assertSeeText('Budi Santoso');
        $createPage->assertSeeText('IT & Digital Product');
        $createPage->assertSeeText('Hendra Wijaya');
    }

    public function test_dashboard_renders_last_3_days_logbook_and_hides_category_filters(): void
    {
        $participant = User::where('role', 'participant')->firstOrFail();

        $response = $this->actingAs($participant)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('Randy Karna');
        $response->assertSeeText('Pencatatan Logbook Kegiatan Harian');
        $response->assertSeeText('Catatan Logbook (3 Hari Terakhir)');
        $response->assertDontSeeText('Tugas Harian');
    }

    public function test_one_logbook_per_day_hides_create_form_and_shows_bento_grid_card(): void
    {
        $participant = User::where('role', 'participant')->firstOrFail();

        $storeRes = $this->actingAs($participant)
            ->post('/logbook', [
                'date' => '2026-11-18',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'title' => 'Penyusunan Dokumen Rekap Harian Bagian',
                'raw_description' => 'menyusun dokumen rekap harian bagian sistem informasi',
                'output' => 'Dokumen Rekap Harian • Arsip Bagian',
                'work_location' => 'kantor',
            ]);

        $storeRes->assertSessionHasNoErrors();
        $newLog = Logbook::where('title', 'Penyusunan Dokumen Rekap Harian Bagian')->firstOrFail();
        $storeRes->assertRedirect(route('logbook.show', $newLog));
        $this->assertEquals('Kantor', $newLog->location_label);

        // Visiting create page for a date that already has a logbook hides the form and shows Grid Card
        $createForFilledDate = $this->actingAs($participant)->get('/logbook/create?date=2026-11-18');
        $createForFilledDate->assertStatus(200);
        $createForFilledDate->assertSeeText('Sudah Tercatat Hari Ini');
        $createForFilledDate->assertSeeText('Penyusunan Dokumen Rekap Harian Bagian');
        $createForFilledDate->assertSeeText('Edit Logbook Ini');
        $createForFilledDate->assertDontSeeText('Simpan Logbook Harian');
    }

    public function test_ai_polish_transcribe_and_offline_sync_endpoints(): void
    {
        $participant = User::where('role', 'participant')->firstOrFail();

        $polishRes = $this->actingAs($participant)
            ->postJson('/api/ai/polish', [
                'text' => 'bikin laporan bulanan dan rapihin dokumen rekap harian',
                'style' => 'formal',
            ]);

        $polishRes->assertStatus(200)
            ->assertJsonStructure(['result', 'quality', 'quota']);

        $syncRes = $this->actingAs($participant)
            ->postJson('/api/logbooks/sync', [
                'items' => [
                    [
                        'client_uuid' => '11111111-2222-3333-4444-555555555555',
                        'date' => '2026-11-19',
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

    public function test_pdf_report_and_qr_verification(): void
    {
        $participant = User::where('role', 'participant')->firstOrFail();
        $report = Report::firstOrFail();

        $pdfRes = $this->actingAs($participant)->get(route('reports.download', $report));
        $pdfRes->assertStatus(200);
        $pdfRes->assertSeeText($report->verify_token);
        $pdfRes->assertSeeText('Rizky Pratama');

        $verifyRes = $this->get(route('reports.verify', $report->verify_token));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSeeText('Dokumen Resmi & Terverifikasi');
    }
}
