<?php

namespace App\Http\Controllers;

use App\Enums\LogStatus;
use App\Models\AiUsage;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $internship = $user->activeInternship()
            ->with(['institution', 'mentor', 'participant'])
            ->first();

        if (! $internship) {
            return redirect()->route('onboarding.show');
        }

        $logbooks = $internship
            ? $internship->logbooks()
                ->with(['attachments', 'comments.user', 'reviewer'])
                ->orderByDesc('date')
                ->orderByDesc('start_time')
                ->get()
            : collect();

        $weekMinutes = $logbooks
            ->filter(fn ($l) => $l->date->between(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()))
            ->sum('duration_minutes');

        if ($weekMinutes === 0 && $logbooks->isNotEmpty()) {
            $weekMinutes = $logbooks->take(4)->sum('duration_minutes');
        }

        $hours = intdiv($weekMinutes, 60);
        $mins = $weekMinutes % 60;
        $heroTimeFormatted = "{$hours}h " . str_pad((string) $mins, 2, '0', STR_PAD_LEFT) . ' m';

        $revisionCount = $logbooks->where('status', LogStatus::Revision)->count();
        $pendingReviewCount = Logbook::where('status', LogStatus::Submitted)->count();

        $adminStats = [
            'participants' => User::where('role', 'participant')->count(),
            'mentors' => User::where('role', 'mentor')->count(),
            'total_logs' => Logbook::count(),
            'ai_calls_today' => AiUsage::whereDate('created_at', today())->count(),
        ];

        $todayStr = Carbon::now('Asia/Jakarta')->toDateString();
        $todayLogbook = $logbooks->first(fn ($l) => $l->date->format('Y-m-d') === $todayStr);

        // Keep only 1 entry per day (unique by date) and take the last 3 days for dashboard
        $recentThreeDaysLogs = $logbooks
            ->unique(fn ($l) => $l->date->format('Y-m-d'))
            ->take(3)
            ->values();

        return view('dashboard.index', compact(
            'user',
            'internship',
            'logbooks',
            'todayLogbook',
            'recentThreeDaysLogs',
            'heroTimeFormatted',
            'revisionCount',
            'pendingReviewCount',
            'adminStats'
        ));
    }
}
