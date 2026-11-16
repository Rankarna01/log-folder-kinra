@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-6 space-y-5">

    <!-- Top App Bar (Matches Screen 1 Top Header in style-design.png) -->
    <header class="flex items-center justify-between">
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 group min-w-0">
            <div class="w-11 h-11 rounded-full bg-[#F8C8C4] border-2 border-white shadow-sm flex items-center justify-center overflow-hidden shrink-0">
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
            <div class="min-w-0">
                <h1 class="text-[15px] font-semibold text-[#111827] group-hover:underline truncate">{{ $user->name }}</h1>
                <p class="text-[11px] text-zinc-500 truncate">
                    {{ $internship?->division ?? 'Divisi Umum' }} • {{ $internship?->position ?? 'Peserta Magang' }}
                </p>
            </div>
        </a>

        <div class="flex items-center gap-2.5 shrink-0">
            <!-- Quick Add Logbook Button -->
            <a href="{{ route('logbook.create') }}"
               title="Tambah Logbook Harian"
               class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-sm hover:scale-105 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                </svg>
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
                <p class="text-xs text-zinc-700 mt-1 font-medium">
                    Mentor: {{ $internship?->mentor?->name ?? 'Agnes Nielsen' }}
                </p>
            </div>
            <a href="{{ route('logbook.create') }}"
               class="bg-white/85 hover:bg-white text-[#18181B] text-[11px] font-semibold px-3 py-1.5 rounded-full shadow-sm transition shrink-0">
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
                    Your task almost done • {{ $logbooks->count() }} kegiatan tercatat
                </p>
            </div>

            <!-- 3D Amber/Yellow Alarm Clock Illustration (Matches style-design.png) -->
            <div class="w-20 h-20 -my-5 -mr-1 flex items-center justify-center select-none">
                <svg class="w-20 h-20 drop-shadow-[0_8px_12px_rgba(0,0,0,0.45)]" viewBox="0 0 96 96" fill="none">
                    <path d="M20 24c-6 4-8 12-4 18l14-14c-4-6-6-6-10-4Z" fill="#F59E0B"/>
                    <path d="M76 24c6 4 8 12 4 18L66 28c4-6 6-6 10-4Z" fill="#F59E0B"/>
                    <rect x="44" y="10" width="8" height="8" rx="3" fill="#D97706"/>
                    <path d="M28 78 22 86M68 78l6 8" stroke="#D97706" stroke-width="5" stroke-linecap="round"/>
                    <circle cx="50" cy="52" r="28" fill="#B45309"/>
                    <circle cx="47" cy="50" r="28" fill="#F59E0B"/>
                    <circle cx="47" cy="50" r="22" fill="#FBBF24" stroke="#FEF3C7" stroke-width="2"/>
                    <path d="M47 32v3M47 65v3M29 50h3M62 50h3" stroke="#92400E" stroke-width="2" stroke-linecap="round"/>
                    <path d="M47 50 40 39M47 50h11" stroke="#FEF3C7" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="47" cy="50" r="2.5" fill="#78350F"/>
                </svg>
            </div>
        </div>
    </section>

    <!-- Quick Action Pills (Tambah Logbook, AI Voice, Cetak PDF) -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-0.5">
        <a href="{{ route('logbook.create') }}"
           class="shrink-0 bg-[#18181B] text-white text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <span>➕</span> Tambah Logbook
        </a>
        <a href="{{ route('logbook.create') }}?voice=1"
           class="shrink-0 bg-white hover:bg-[#E5D9FA] text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <span>🎤</span> Dikte Suara AI
        </a>
        <a href="{{ route('reports.index') }}"
           class="shrink-0 bg-white hover:bg-[#FBE8A6] text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <span>📄</span> Cetak PDF
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
            ['bg' => 'bg-[#D5EDE8]', 'bar' => 'bg-[#B4DCD3]', 'pct' => 85],
            ['bg' => 'bg-[#E6E8FA]', 'bar' => 'bg-[#C7CCF4]', 'pct' => 75],
            ['bg' => 'bg-[#FBE8A6]', 'bar' => 'bg-[#F5D061]', 'pct' => 90],
            ['bg' => 'bg-[#E5D9FA]', 'bar' => 'bg-[#D5C2F7]', 'pct' => 80],
        ];
    @endphp

    <div class="space-y-4">
        @forelse ($logbooks->take(8) as $index => $log)
            @php
                $palette = $cardPalettes[$index % count($cardPalettes)];
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

                    <a href="{{ route('logbook.edit', $log) }}"
                       title="Edit Logbook"
                       class="w-8 h-8 rounded-full bg-white/60 hover:bg-white flex items-center justify-center text-[#18181B] shrink-0 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z"/>
                        </svg>
                    </a>
                </div>

                <!-- Row 2: Duration + Category Pill (No Admin/Approval Status) -->
                <div class="flex items-center gap-2.5 mt-3">
                    <span class="inline-flex items-center gap-1.5 text-xs text-zinc-600 font-medium">
                        <svg class="w-3.5 h-3.5 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="13" r="8"/>
                            <path d="M12 9v4l2.5 1.5M9 2h6"/>
                        </svg>
                        {{ substr($log->start_time, 0, 5) }} - {{ substr($log->end_time, 0, 5) }} ({{ $log->formatted_duration }})
                    </span>

                    <span class="inline-flex items-center gap-1 bg-white px-3 py-1 rounded-full text-xs font-semibold text-[#18181B] shadow-2xs">
                        <span>⚑</span> {{ $log->category->label() }}
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
                         style="width: {{ $palette['pct'] }}%"></div>
                </div>

                <!-- Row 4: Stacked Avatars +3 | Date Pill | Comment Count Pill -->
                <div class="mt-3.5 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <div class="flex -space-x-2">
                            <span class="w-6 h-6 rounded-full bg-[#F8C8C4] border-2 border-white flex items-center justify-center text-[10px] font-bold">{{ $user->initials }}</span>
                            <span class="w-6 h-6 rounded-full bg-[#D5C2F7] border-2 border-white flex items-center justify-center text-[10px] font-bold">M</span>
                        </div>
                        <span class="text-xs font-semibold text-[#111827] ml-0.5">{{ strtoupper($log->work_location ?? 'WFO') }}</span>
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

                        <!-- White Notes Count Pill -->
                        <a href="{{ route('logbook.show', $log) }}#comments"
                           class="inline-flex items-center gap-1 bg-white px-2.5 py-1.5 rounded-full text-xs font-semibold text-[#111827] shadow-2xs">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3C6.5 3 2 6.6 2 11c0 2.1 1 4 2.7 5.4L4 21l4.8-1.9c1 .3 2.1.4 3.2.4 5.5 0 10-3.6 10-8.5S17.5 3 12 3Z"/>
                            </svg>
                            <span>{{ str_pad((string) max(1, $log->comments->count()), 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white rounded-[28px] p-8 text-center">
                <p class="text-sm font-semibold text-zinc-700">Belum ada logbook harian tercatat.</p>
                <a href="{{ route('logbook.create') }}" class="mt-3 inline-block bg-[#18181B] text-white text-xs font-semibold px-4 py-2.5 rounded-full">
                    + Tambah Logbook Harian
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
