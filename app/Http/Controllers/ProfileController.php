<?php

namespace App\Http\Controllers;

use App\Models\ActivityTemplate;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user() ?? User::firstOrFail();
        $internship = $user->activeInternship()->with(['institution', 'mentor'])->first()
            ?? Internship::with(['institution', 'mentor'])->first();
        $templates = $user->activityTemplates()->latest()->get();
        $allUsers = User::orderBy('id')->get();

        return view('profile.show', compact('user', 'internship', 'templates', 'allUsers'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user() ?? User::firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'nim_nik' => ['nullable', 'string', 'max:50'],
            'campus' => ['nullable', 'string', 'max:150'],
            'major' => ['nullable', 'string', 'max:150'],
            'ai_style' => ['nullable', 'string', 'in:formal,singkat,detail'],
            'reminder_time' => ['nullable', 'string'],
        ]);

        $prefs = $user->preferences ?? [];
        $prefs['ai_style'] = $validated['ai_style'] ?? 'formal';
        $prefs['reminder_time'] = $validated['reminder_time'] ?? '16:00';

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'nim_nik' => $validated['nim_nik'] ?? $user->nim_nik,
            'campus' => $validated['campus'] ?? $user->campus,
            'major' => $validated['major'] ?? $user->major,
            'preferences' => $prefs,
        ]);

        return back()->with('toast', 'Profil & preferensi AI berhasil diperbarui!');
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
            fputcsv($out, ['Tanggal', 'Jam Mulai', 'Jam Selesai', 'Durasi (Menit)', 'Judul', 'Kategori', 'Deskripsi Final', 'Output', 'Status']);
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
                    $entry->status->label(),
                ]);
            }
            fclose($out);
        }, 'rekap-logbook-maganghub.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
