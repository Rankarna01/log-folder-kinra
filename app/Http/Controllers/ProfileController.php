<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\ActivityTemplate;
use App\Models\Institution;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user() ?? User::where('role', Role::Participant->value)->first() ?? User::firstOrFail();
        $internship = $user->activeInternship()->with(['institution', 'mentor'])->first()
            ?? Internship::with(['institution', 'mentor'])->first();
        $templates = $user->activityTemplates()->latest()->get();

        return view('profile.show', compact('user', 'internship', 'templates'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user() ?? User::where('role', Role::Participant->value)->first() ?? User::firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'division' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:150'],
            'mentor_name' => ['required', 'string', 'max:150'],
            'ai_style' => ['nullable', 'string', 'in:formal,singkat,detail'],
        ]);

        $prefs = $user->preferences ?? [];
        $prefs['ai_style'] = $validated['ai_style'] ?? 'formal';
        $prefs['mentor_name'] = trim($validated['mentor_name']);
        $prefs['division'] = trim($validated['division']);
        $prefs['position'] = trim($validated['position']);

        $user->update([
            'name' => trim($validated['name']),
            'campus' => trim($validated['division']),
            'major' => trim($validated['position']),
            'preferences' => $prefs,
        ]);

        $mentor = User::firstOrCreate(
            [
                'name' => trim($validated['mentor_name']),
                'role' => Role::Mentor->value,
            ],
            [
                'email' => 'mentor.' . Str::slug($validated['mentor_name']) . '.' . Str::lower(Str::random(4)) . '@maganghub.local',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $internship = $user->activeInternship()->first();
        if ($internship) {
            $internship->update([
                'division' => trim($validated['division']),
                'position' => trim($validated['position']),
                'mentor_id' => $mentor->id,
            ]);
        } else {
            $institution = Institution::firstOrCreate(['name' => 'PT Kinra Digital Nusantara (MagangHub)']);
            Internship::create([
                'participant_id' => $user->id,
                'mentor_id' => $mentor->id,
                'institution_id' => $institution->id,
                'division' => trim($validated['division']),
                'position' => trim($validated['position']),
                'start_date' => today()->startOfMonth()->toDateString(),
                'end_date' => today()->addMonths(3)->endOfMonth()->toDateString(),
                'work_days' => [1, 2, 3, 4, 5],
                'status' => 'active',
            ]);
        }

        return back()->with('toast', 'Data peserta (Nama, Divisi, Bagian, & Mentor) berhasil diperbarui!');
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $user = $request->user() ?? User::firstOrFail();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        ActivityTemplate::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
        ]);

        return back()->with('toast', 'Template kegiatan berulang berhasil disimpan.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user() ?? User::firstOrFail();
        $internship = $user->activeInternship ?? Internship::first();
        $entries = $internship ? $internship->logbooks()->orderBy('date')->get() : collect();

        return response()->streamDownload(function () use ($entries) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Jam Mulai', 'Jam Selesai', 'Durasi (Menit)', 'Judul', 'Kategori', 'Deskripsi Final', 'Output', 'Lokasi']);
            foreach ($entries as $entry) {
                fputcsv($out, [
                    $entry->date->format('Y-m-d'),
                    $entry->start_time,
                    $entry->end_time,
                    $entry->duration_minutes,
                    $entry->title,
                    $entry->category->label(),
                    $entry->final_description,
                    $entry->output,
                    strtoupper($entry->work_location ?? 'WFO'),
                ]);
            }
            fclose($out);
        }, 'rekap-logbook-maganghub.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
