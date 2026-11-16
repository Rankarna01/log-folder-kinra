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
    public function showRegister(): View
    {
        $recentParticipants = User::where('role', Role::Participant->value)
            ->with('activeInternship.mentor')
            ->latest()
            ->take(5)
            ->get();

        return view('auth.register', compact('recentParticipants'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'division' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:150'],
            'mentor_name' => ['required', 'string', 'max:150'],
        ]);

        $institution = Institution::firstOrCreate(
            ['name' => 'PT Kinra Digital Nusantara (MagangHub)'],
            [
                'address' => 'Jakarta Selatan, Indonesia',
                'letterhead' => [
                    'subtitle' => 'Logbook Kegiatan Harian Peserta Magang',
                ],
            ]
        );

        $mentor = User::firstOrCreate(
            [
                'name' => trim($validated['mentor_name']),
                'role' => Role::Mentor->value,
            ],
            [
                'email' => 'mentor.' . Str::slug($validated['mentor_name']) . '.' . Str::lower(Str::random(4)) . '@maganghub.local',
                'password' => Hash::make('password'),
                'campus' => $validated['division'],
                'major' => $validated['position'],
                'is_active' => true,
            ]
        );

        $participant = User::create([
            'name' => trim($validated['name']),
            'email' => Str::slug($validated['name']) . '.' . Str::lower(Str::random(5)) . '@peserta.maganghub.local',
            'password' => Hash::make('password'),
            'role' => Role::Participant,
            'campus' => $validated['division'],
            'major' => $validated['position'],
            'preferences' => [
                'ai_style' => 'formal',
                'language' => 'id',
                'mentor_name' => trim($validated['mentor_name']),
                'division' => trim($validated['division']),
                'position' => trim($validated['position']),
            ],
            'is_active' => true,
        ]);

        Internship::create([
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
            'institution_id' => $institution->id,
            'division' => trim($validated['division']),
            'position' => trim($validated['position']),
            'start_date' => today()->startOfMonth()->toDateString(),
            'end_date' => today()->addMonths(3)->endOfMonth()->toDateString(),
            'work_days' => [1, 2, 3, 4, 5],
            'status' => 'active',
        ]);

        Auth::login($participant, true);
        $request->session()->regenerate();

        return redirect()
            ->route('logbook.create')
            ->with('toast', "Selamat datang, {$participant->name}! Silakan isi logbook harianmu.");
    }

    public function quickLoginParticipant(Request $request, User $user): RedirectResponse
    {
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()
            ->route('logbook.create')
            ->with('toast', "Masuk sebagai {$user->name}. Silakan tambah logbook harian.");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register');
    }
}
