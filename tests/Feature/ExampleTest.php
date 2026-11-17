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

    protected function registerAndOnboardParticipant(
        string $email = 'budi.santoso@kinra.id',
        string $name = 'Budi Santoso',
        string $division = 'IT & Digital Product',
        string $position = 'Fullstack Web Developer Intern',
        string $mentorName = 'Hendra Wijaya'
    ): User {
        $this->post('/register', [
            'email' => $email,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $this->post(route('onboarding.store'), [
            'name' => $name,
            'division' => $division,
            'position' => $position,
            'mentor_name' => $mentorName,
        ]);

        return User::where('email', $email)->firstOrFail();
    }

    public function test_first_visit_has_no_default_users_and_redirects_to_login(): void
    {
        $this->assertEquals(0, User::count());
        $this->assertEquals(0, Logbook::count());

        $homeRes = $this->get('/');
        $homeRes->assertRedirect(route('login'));

        $loginPage = $this->get('/login');
        $loginPage->assertStatus(200);
        $loginPage->assertDontSeeText('Esther Howard');
    }

    public function test_two_step_registration_and_persistent_relogin_with_email_and_password(): void
    {
        $registerPage = $this->get('/register');
        $registerPage->assertStatus(200);
        $registerPage->assertSeeText('Daftar Akun Peserta Baru');

        $step1Res = $this->post('/register', [
            'email' => 'budi.santoso@kinra.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $step1Res->assertRedirect(route('onboarding.show'));
        $this->assertAuthenticated();
        $this->assertEquals('budi.santoso@kinra.id', auth()->user()->email);

        $onboardingPage = $this->get(route('onboarding.show'));
        $onboardingPage->assertStatus(200);
        $onboardingPage->assertSeeText('Lengkapi Data Peserta Magang');

        $step2Res = $this->post(route('onboarding.store'), [
            'name' => 'Budi Santoso',
            'division' => 'IT & Digital Product',
            'position' => 'Fullstack Web Developer Intern',
            'mentor_name' => 'Hendra Wijaya',
        ]);

        $step2Res->assertRedirect(route('dashboard'));

        $this->post('/logbook', [
            'date' => '2026-11-16',
            'start_time' => '08:30',
            'end_time' => '12:00',
            'title' => 'Pengerjaan Modul Autentikasi Peserta',
            'raw_description' => 'Membuat fitur login dan registrasi peserta magang',
            'output' => 'Modul Login & Registrasi',
            'work_location' => 'kantor',
        ]);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();

        $loginRes = $this->post('/login', [
            'email' => 'budi.santoso@kinra.id',
            'password' => 'rahasia123',
        ]);

        $loginRes->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('Budi Santoso', auth()->user()->name);

        $dashboard = $this->get('/dashboard');
        $dashboard->assertStatus(200);
        $dashboard->assertSeeText('Budi Santoso');
        $dashboard->assertSeeText('IT & Digital Product');
        $dashboard->assertSeeText('Pengerjaan Modul Autentikasi Peserta');
    }

    public function test_one_logbook_per_day_hides_create_form_and_shows_bento_grid_card(): void
    {
        $participant = $this->registerAndOnboardParticipant();

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

        $createForFilledDate = $this->actingAs($participant)->get('/logbook/create?date=2026-11-18');
        $createForFilledDate->assertStatus(200);
        $createForFilledDate->assertSeeText('Sudah Tercatat Hari Ini');
        $createForFilledDate->assertSeeText('Penyusunan Dokumen Rekap Harian Bagian');
        $createForFilledDate->assertSeeText('Edit Logbook Ini');
        $createForFilledDate->assertDontSeeText('Simpan Logbook Harian');
    }

    public function test_ai_polish_transcribe_and_offline_sync_endpoints(): void
    {
        $participant = $this->registerAndOnboardParticipant();

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

    public function test_pdf_report_generation_and_qr_verification(): void
    {
        $participant = $this->registerAndOnboardParticipant();

        $this->actingAs($participant)->post('/reports', [
            'type' => 'monthly',
            'period_start' => '2026-11-01',
            'period_end' => '2026-11-30',
            'include_photos' => true,
        ]);

        $report = Report::firstOrFail();

        $pdfRes = $this->actingAs($participant)->get(route('reports.download', $report));
        $pdfRes->assertStatus(200);
        $pdfRes->assertSeeText($report->verify_token);
        $pdfRes->assertSeeText('Hendra Wijaya');

        $verifyRes = $this->get(route('reports.verify', $report->verify_token));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSeeText('Dokumen Resmi dan Terverifikasi');
    }
}
