<?php

namespace App\Http\Controllers;

use App\Models\AiUsage;
use App\Models\Institution;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $users = User::with('internships')->orderBy('id')->get();
        $institutions = Institution::withCount('internships')->get();
        $internships = Internship::with(['participant', 'mentor', 'institution', 'logbooks'])->get();
        $aiStats = [
            'total_calls' => AiUsage::count(),
            'today_calls' => AiUsage::whereDate('created_at', today())->count(),
            'daily_limit' => config('services.gemini.daily_limit', 30),
            'model' => config('services.gemini.model', 'gemini-flash-latest'),
        ];
        $totalLogs = Logbook::count();

        return view('admin.index', compact('users', 'institutions', 'internships', 'aiStats', 'totalLogs'));
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'max_backdate_days' => ['required', 'integer', 'min:0', 'max:30'],
            'ai_daily_limit' => ['required', 'integer', 'min:1', 'max:200'],
            'institution_name' => ['required', 'string', 'max:150'],
        ]);

        Setting::updateOrCreate(['key' => 'system_config'], ['value' => $validated]);

        $inst = Institution::first();
        if ($inst) {
            $inst->update(['name' => $validated['institution_name']]);
        }

        return back()->with('toast', 'Pengaturan global & kuota AI berhasil disimpan.');
    }
}
