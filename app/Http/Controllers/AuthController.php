<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Institution;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            $user = $request->user();

            if (! $user->activeInternship()->exists()) {
                return redirect()
                    ->route('onboarding.show')
                    ->with('toast', 'Silakan lengkapi data peserta magang terlebih dahulu.');
            }

            return redirect()
                ->route('dashboard')
                ->with('toast', "Selamat datang kembali, {$user->name}!");
        }

        return back()
            ->withErrors(['email' => 'Email atau password yang dimasukkan tidak sesuai.'])
            ->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Step 1: Register account with Email + Password (2x confirmation),
     * then redirect to Step 2 (/onboarding) to fill Nama, Divisi, Bagian, & Mentor.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'name' => ['nullable', 'string', 'max:150'],
            'division' => ['nullable', 'string', 'max:150'],
            'position' => ['nullable', 'string', 'max:150'],
            'mentor_name' => ['nullable', 'string', 'max:150'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan langsung masuk (Login).',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password (ulangi password) tidak sama.',
        ]);

        $defaultName = ! empty($validated['name'])
            ? trim($validated['name'])
            : Str::headline( explode('@', $validated['email'])[0]);

        $participant = User::create([
            'name' => $defaultName,
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => Role::Participant,
            'is_active' => true,
        ]);

        Auth::login($participant, true);
        $request->session()->regenerate();

        // If the request also included onboarding fields directly, save them right away
        if (! empty($validated['division']) && ! empty($validated['position']) && ! empty($validated['mentor_name'])) {
            $this->saveParticipantInternship($participant, [
                'name' => $defaultName,
                'division' => $validated['division'],
                'position' => $validated['position'],
                'mentor_name' => $validated['mentor_name'],
            ]);

            return redirect()
                ->route('logbook.create')
                ->with('toast', "Selamat datang, {$participant->name}! Silakan isi logbook harianmu.");
        }

        return redirect()
            ->route('onboarding.show')
            ->with('toast', 'Akun berhasil dibuat! Sekarang lengkapi data peserta magangmu.');
    }

    /**
     * Step 2: Fill Participant Details (Nama, Divisi, Bagian, Mentor)
     */
    public function showOnboarding(Request $request): View
    {
        $user = $request->user();
        $internship = $user?->activeInternship()->with('mentor')->first();

        return view('auth.onboarding', compact('user', 'internship'));
    }

    public function storeOnboarding(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'division' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:150'],
            'mentor_name' => ['required', 'string', 'max:150'],
        ]);

        $user = $request->user();
        $this->saveParticipantInternship($user, $validated);

        return redirect()
            ->route('logbook.create')
            ->with('toast', "Data peserta tersimpan! Silakan catat logbook harianmu, {$user->fresh()->name}.");
    }

    protected function saveParticipantInternship(User $participant, array $data): void
    {
        $institution = Institution::firstOrCreate(
            ['name' => 'PT Kawasan Industri Nusantara (KINRA Sei Mangkei)'],
            [
                'address' => 'KEK Sei Mangkei, Sumatera Utara',
                'letterhead' => [
                    'subtitle' => 'Logbook Kegiatan Harian Peserta Magang',
                ],
            ]
        );

        $mentor = User::firstOrCreate(
            [
                'name' => trim($data['mentor_name']),
                'role' => Role::Mentor->value,
            ],
            [
                'email' => 'mentor.' . Str::slug($data['mentor_name']) . '.' . Str::lower(Str::random(4)) . '@kinra.local',
                'password' => Hash::make('password'),
                'campus' => trim($data['division']),
                'major' => trim($data['position']),
                'is_active' => true,
            ]
        );

        $participant->update([
            'name' => trim($data['name']),
            'campus' => trim($data['division']),
            'major' => trim($data['position']),
            'preferences' => [
                'ai_style' => 'formal',
                'language' => 'id',
                'mentor_name' => trim($data['mentor_name']),
                'division' => trim($data['division']),
                'position' => trim($data['position']),
            ],
        ]);

        Internship::updateOrCreate(
            [
                'participant_id' => $participant->id,
                'status' => 'active',
            ],
            [
                'mentor_id' => $mentor->id,
                'institution_id' => $institution->id,
                'division' => trim($data['division']),
                'position' => trim($data['position']),
                'start_date' => today()->startOfMonth()->toDateString(),
                'end_date' => today()->addMonths(3)->endOfMonth()->toDateString(),
                'work_days' => [1, 2, 3, 4, 5],
            ]
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('toast', 'Berhasil keluar akun. Silakan masuk kembali dengan email & passwordmu.');
    }
}
