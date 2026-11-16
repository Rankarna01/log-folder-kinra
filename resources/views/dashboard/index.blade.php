@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-6 space-y-5">

    <!-- Top App Bar (Matches Screen 1 Top Header in style-design.png) -->
    <header class="flex items-center justify-between">
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-full bg-[#F8C8C4] border-2 border-white shadow-sm flex items-center justify-center overflow-hidden">
                <svg class="w-9 h-9 mt-1" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="24" r="12" fill="#4A2C2A"/>
                    <circle cx="32" cy="26" r="10" fill="#F3C6B6"/>
                    <path d="M22 24c0-6 4.5-10 10-10s10 4 10 10c-3-2-6-3-10-3s-7 1-10 3Z" fill="#2D1B18"/>
                    <circle cx="28.5" cy="25.5" r="1.3" fill="#18181B"/>
                    <circle cx="35.5" cy="25.5" r="1.3" fill="#18181B"/>
                    <path d="M28 30c2 1.8 6 1.8 8 0" stroke="#18181B" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M16 56c2-10 9-15 16-15s14 5 16 15" fill="#18181B"/>
                </svg>
            </div>
            <div>
                <h1 class="text-[15px] font-semibold text-[#111827] group-hover:underline">{{ $user->name }}</h1>
                <p class="text-[11px] text-zinc-500">{{ $user->role->label() }} • {{ $internship?->division ?? 'Digital Product' }}</p>
            </div>
        </a>

        <div class="flex items-center gap-2.5">
            <!-- Notification Bell Pill Button -->
            <a href="{{ $user->isMentor() ? route('review.index') : route('logbook.index', ['status' => 'revision']) }}"
               title="Notifikasi & Revisi"
               class="relative w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-sm hover:scale-105 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a7 7 0 0 0-7 7v4.3l-1.6 2.4A1 1 0 0 0 4.2 17h15.6a1 1 0 0 0 .8-1.3L19 13.3V9a7 7 0 0 0-7-7Zm0 20a3 3 0 0 0 2.8-2H9.2A3 3 0 0 0 12 22Z"/>
                </svg>
                @if ($revisionCount > 0 || $pendingReviewCount > 0)
                    <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                @endif
            </a>

            <!-- Search Button -->
            <a href="{{ route('logbook.index') }}"
               title="Cari Logbook"
               class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-sm hover:scale-105 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </header>

    <!-- Hero Lavender Grid Card with Dark Timer Island & 3D Yellow Alarm Clock (Screen 1) -->
    <section class="bg-lavender-grid rounded-[28px] p-5 relative overflow-hidden shadow-sm">
        <div class="flex items-start justify-between gap-2">
            <div>
                <h2 class="text-[20px] font-semibold text-[#111827] leading-[1.3] max-w-[240px]">
                    Mastering projects with management
                </h2>
                @if ($internship)
                    @php $prog = $internship->progress_days; @endphp
                    <p class="text-xs text-zinc-700 mt-1 font-medium">
                        Hari ke-{{ $prog['current'] }} dari {{ $prog['total'] }} ({{ $prog['percentage'] }}% periode magang)
                    </p>
                @endif
            </div>
            <a href="{{ route('logbook.create') }}"
               class="bg-white/80 hover:bg-white text-[#18181B] text-[11px] font-semibold px-3 py-1.5 rounded-full shadow-sm transition">
                + Isi Hari Ini
            </a>
        </div>

        <!-- Dark Charcoal Inner Timer Card + Pop-out 3D Yellow Clock -->
        <div class="mt-5 bg-[#18181B] rounded-[22px] px-5 py-4 flex items-center justify-between relative">
            <div>
                <div class="text-[24px] font-bold text-white tracking-tight leading-tight">
                    {{ $heroTimeFormatted }}
                </div>
                <p class="text-xs text-zinc-400 mt-1">
                    Your task almost done • {{ $logbooks->count() }} entri tercatat
                </p>
            </div>

            <!-- 3D Amber/Yellow Alarm Clock Illustration (Matches style-design.png) -->
            <div class="w-20 h-20 -my-5 -mr-1 flex items-center justify-center select-none">
                <svg class="w-20 h-20 drop-shadow-[0_8px_12px_rgba(0,0,0,0.45)]" viewBox="0 0 96 96" fill="none">
                    <!-- Twin Bells -->
                    <path d="M20 24c-6 4-8 12-4 18l14-14c-4-6-6-6-10-4Z" fill="#F59E0B"/>
                    <path d="M76 24c6 4 8 12 4 18L66 28c4-6 6-6 10-4Z" fill="#F59E0B"/>
                    <rect x="44" y="10" width="8" height="8" rx="3" fill="#D97706"/>
                    <!-- Feet -->
                    <path d="M28 78 22 86M68 78l6 8" stroke="#D97706" stroke-width="5" stroke-linecap="round"/>
                    <!-- Outer 3D Body -->
                    <circle cx="50" cy="52" r="28" fill="#B45309"/>
                    <circle cx="47" cy="50" r="28" fill="#F59E0B"/>
                    <circle cx="47" cy="50" r="22" fill="#FBBF24" stroke="#FEF3C7" stroke-width="2"/>
                    <!-- Clock Ticks & Hands -->
                    <path d="M47 32v3M47 65v3M29 50h3M62 50h3" stroke="#92400E" stroke-width="2" stroke-linecap="round"/>
                    <path d="M47 50 40 39M47 50h11" stroke="#FEF3C7" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="47" cy="50" r="2.5" fill="#78350F"/>
                </svg>
            </div>
        </div>
    </section>

    <!-- Role Context Quick Cards (Mentor / Admin / Revision Alert) -->
    @if ($user->isMentor() && $pendingReviewCount > 0)
        <div class="bg-[#D5EDE8] rounded-[24px] p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">Mode Mentor Pembimbing</p>
                <p class="text-sm font-semibold text-[#111827] mt-0.5">{{ $pendingReviewCount }} logbook menunggu persetujuan Anda</p>
            </div>
            <a href="{{ route('review.index') }}" class="bg-[#18181B] text-white text-xs font-semibold px-4 py-2 rounded-full">
                Review Sekarang
            </a>
        </div>
    @elseif ($user->isAdmin())
        <div class="grid grid-cols-3 gap-2.5">
            <div class="bg-[#D5EDE8] rounded-[22px] p-3.5 text-center">
                <p class="text-[11px] text-zinc-600 font-medium">Peserta Aktif</p>
                <p class="text-lg font-bold text-[#111827]">{{ $adminStats['participants'] }}</p>
            </div>
            <div class="bg-[#E6E8FA] rounded-[22px] p-3.5 text-center">
                <p class="text-[11px] text-zinc-600 font-medium">Total Logbook</p>
                <p class="text-lg font-bold text-[#111827]">{{ $adminStats['total_logs'] }}</p>
            </div>
            <div class="bg-[#FBE8A6] rounded-[22px] p-3.5 text-center">
                <p class="text-[11px] text-zinc-600 font-medium">Panggilan AI</p>
                <p class="text-lg font-bold text-[#111827]">{{ $adminStats['ai_calls_today'] }}/30</p>
            </div>
        </div>
    @elseif ($revisionCount > 0)
        <div class="bg-[#FBE8A6] rounded-[24px] p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-[12px] bg-white flex items-center justify-center font-bold text-amber-800">!</span>
                <div>
                    <p class="text-xs font-bold text-[#111827]">Ada {{ $revisionCount }} Entri Perlu Revisi</p>
                    <p class="text-[11px] text-zinc-700">Cek catatan dari Mentor Agnes Nielsen</p>
                </div>
            </div>
            <a href="{{ route('logbook.index', ['status' => 'revision']) }}" class="bg-[#18181B] text-white text-xs font-semibold px-3.5 py-1.5 rounded-full">
                Perbaiki
            </a>
        </div>
    @endif

    <!-- Quick Action Pills (AI Voice, Template, Cetak PDF) -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-0.5">
        <a href="{{ route('logbook.create') }}?voice=1"
           class="shrink-0 bg-white hover:bg-[#E5D9FA] text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <span>🎤</span> Dikte Suara AI
        </a>
        <a href="{{ route('logbook.create') }}"
           class="shrink-0 bg-white hover:bg-[#D5EDE8] text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <span>✨</span> Rapikan Gemini
        </a>
        <a href="{{ route('reports.index') }}"
           class="shrink-0 bg-white hover:bg-[#FBE8A6] text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <span>📄</span> Cetak PDF Bulanan
        </a>
    </div>

    <!-- Section Header: "Your task" + "See All" (Exact Screen 1 layout) -->
    <div class="flex items-center justify-between pt-1">
        <h3 class="text-[17px] font-semibold text-[#111827]">Your task</h3>
        <a href="{{ route('logbook.index') }}" class="text-xs font-medium text-zinc-400 hover:text-[#111827]">See All</a>
    </div>

    <!-- Colored Pastel Logbook Cards (Mint, Periwinkle, Butter Yellow, Lavender) -->
    @php
        $cardPalettes = [
            ['bg' => 'bg-[#D5EDE8]', 'bar' => 'bg-[#B4DCD3]', 'priority' => 'High'],
            ['bg' => 'bg-[#E6E8FA]', 'bar' => 'bg-[#C7CCF4]', 'priority' => 'High'],
            ['bg' => 'bg-[#FBE8A6]', 'bar' => 'bg-[#F5D061]', 'priority' => 'Medium'],
            ['bg' => 'bg-[#E5D9FA]', 'bar' => 'bg-[#D5C2F7]', 'priority' => 'High'],
        ];
    @endphp

    <div class="space-y-4">
        @forelse ($logbooks->take(8) as $index => $log)
            @php
                $palette = $cardPalettes[$index % count($cardPalettes)];
                $styleInfo = $log->status->cardStyle();
            @endphp
            <article class="{{ $palette['bg'] }} rounded-[28px] p-4 transition hover:translate-y-[-2px] shadow-sm">
                <!-- Row 1: White Squircle Icon + Title + Edit Pencil Circle -->
                <div class="flex items-center justify-between gap-2">
                    <a href="{{ route('logbook.show', $log) }}" class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-[12px] bg-white flex items-center justify-center text-[#18181B] shrink-0 shadow-xs">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="3" y="4" width="18" height="16" rx="3"/>
                                <path d="M3 9h18" stroke="#ffffff" stroke-width="2"/>
                            </svg>
                        </span>
                        <h4 class="text-[15px] font-semibold text-[#111827] truncate">
                            {{ $log->title }}
                        </h4>
                    </a>

                    <a href="{{ $log->isEditable() ? route('logbook.edit', $log) : route('logbook.show', $log) }}"
                       title="Detail / Edit"
                       class="w-8 h-8 rounded-full bg-white/60 hover:bg-white flex items-center justify-center text-[#18181B] shrink-0 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z"/>
                        </svg>
                    </a>
                </div>

                <!-- Row 2: Ongoing / Duration + White Status/Priority Pill -->
                <div class="flex items-center gap-3 mt-3">
                    <span class="inline-flex items-center gap-1.5 text-xs text-zinc-600 font-medium">
                        <svg class="w-3.5 h-3.5 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="13" r="8"/>
                            <path d="M12 9v4l2.5 1.5M9 2h6"/>
                        </svg>
                        Ongoing • {{ $log->formatted_duration }}
                    </span>

                    <span class="inline-flex items-center gap-1 bg-white px-3 py-1 rounded-full text-xs font-semibold text-[#18181B] shadow-2xs">
                        <span>⚑</span> {{ $log->status->label() }}
                    </span>

                    @if ($log->ai_description)
                        <span class="inline-flex items-center gap-1 bg-white/70 px-2.5 py-1 rounded-full text-[11px] font-semibold text-purple-900">
                            ✨ AI
                        </span>
                    @endif
                </div>

                <!-- Row 3: White Progress Bar Track + Tonal Pastel Fill -->
                <div class="mt-3.5 w-full h-2.5 bg-white/85 rounded-full overflow-hidden p-0.5">
                    <div class="{{ $palette['bar'] }} h-full rounded-full transition-all"
                         style="width: {{ $styleInfo['progress'] }}%"></div>
                </div>

                <!-- Row 4: Stacked Avatars +3 | Date Pill | Comment Count Pill -->
                <div class="mt-3.5 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <div class="flex -space-x-2">
                            <span class="w-6 h-6 rounded-full bg-[#F8C8C4] border-2 border-white flex items-center justify-center text-[10px] font-bold">E</span>
                            <span class="w-6 h-6 rounded-full bg-[#D5C2F7] border-2 border-white flex items-center justify-center text-[10px] font-bold">A</span>
                            <span class="w-6 h-6 rounded-full bg-[#FBE8A6] border-2 border-white flex items-center justify-center text-[10px] font-bold">R</span>
                        </div>
                        <span class="text-xs font-semibold text-[#111827] ml-0.5">+{{ max(1, $log->attachments->count() + 2) }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <!-- White Date Pill -->
                        <a href="{{ route('logbook.show', $log) }}"
                           class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full text-xs font-semibold text-[#111827] shadow-2xs">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="17" rx="3"/>
                                <path d="M3 9h18"/>
                            </svg>
                            <span>{{ $log->date->format('d F') }}</span>
                            <span class="text-zinc-400">⌄</span>
                        </a>

                        <!-- White Comment Count Pill -->
                        <a href="{{ route('logbook.show', $log) }}#comments"
                           class="inline-flex items-center gap-1 bg-white px-2.5 py-1.5 rounded-full text-xs font-semibold text-[#111827] shadow-2xs">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3C6.5 3 2 6.6 2 11c0 2.1 1 4 2.7 5.4L4 21l4.8-1.9c1 .3 2.1.4 3.2.4 5.5 0 10-3.6 10-8.5S17.5 3 12 3Z"/>
                            </svg>
                            <span>{{ str_pad((string) max(2, $log->comments->count()), 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white rounded-[28px] p-8 text-center">
                <p class="text-sm font-semibold text-zinc-700">Belum ada logbook tercatat.</p>
                <a href="{{ route('logbook.create') }}" class="mt-3 inline-block bg-[#18181B] text-white text-xs font-semibold px-4 py-2.5 rounded-full">
                    + Buat Entri Pertama
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
