@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-8 space-y-4">

    <!-- Month Navigator Header -->
    @php
        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
    @endphp
    <header class="flex items-center justify-between">
        <a href="{{ route('logbook.index', ['month' => $prevMonth]) }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0B6E6C] shadow-2xs hover:scale-105 transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

        <h1 class="text-[17px] font-semibold text-[#111827]">
            {{ $currentMonth->format('F Y') }}
        </h1>

        <a href="{{ route('logbook.index', ['month' => $nextMonth]) }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0B6E6C] shadow-2xs hover:scale-105 transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </header>

    <!-- Calendar Grid Card with KINRA Green & Amber States -->
    <section class="bg-white rounded-[28px] p-4 shadow-2xs">
        <div class="grid grid-cols-7 gap-1.5 text-center mb-2">
            @foreach (['Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb', 'Mg'] as $dayLabel)
                <span class="text-[10px] font-semibold text-zinc-400 uppercase">{{ $dayLabel }}</span>
            @endforeach
        </div>

        <div class="grid grid-cols-7 gap-2 text-center">
            @foreach ($calendarDays as $cell)
                @php
                    if ($cell['is_selected']) {
                        $cellClasses = 'bg-[#0B6E6C] text-white font-bold shadow-xs';
                    } elseif ($cell['is_filled']) {
                        $cellClasses = 'bg-[#FDE9B4] text-[#111827] font-semibold';
                    } elseif (! $cell['in_month']) {
                        $cellClasses = 'bg-[#F3F4F6] text-zinc-400 font-medium';
                    } else {
                        $cellClasses = 'bg-transparent text-[#111827] font-semibold hover:bg-zinc-100';
                    }
                @endphp
                <a href="{{ route('logbook.index', ['month' => $currentMonth->format('Y-m'), 'date' => $cell['date']]) }}"
                   class="aspect-square rounded-[13px] flex items-center justify-center text-xs transition {{ $cellClasses }}">
                    {{ $cell['day'] }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- "Today's meeting" / Kegiatan Harian Card -->
    <section class="bg-white rounded-[28px] p-5 shadow-2xs space-y-3.5">
        <div class="flex items-center justify-between">
            <h2 class="text-[16px] font-semibold text-[#111827]">Today's meeting & logbook</h2>
            <span class="text-xs text-zinc-400">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</span>
        </div>

        @forelse ($selectedDayLogs as $idx => $item)
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-8 h-8 rounded-full bg-[#0B6E6C] text-white flex items-center justify-center text-xs font-bold shrink-0">
                        {{ $internship?->participant?->initials ?? 'EH' }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-[#111827] truncate">{{ $item->title }}</p>
                        <p class="text-[11px] text-zinc-500 flex items-center gap-1 mt-0.5">
                            <span>🕒</span>
                            <span>{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB</span>
                        </p>
                    </div>
                </div>

                <a href="{{ route('logbook.show', $item) }}"
                   class="shrink-0 {{ $idx === 0 ? 'bg-[#0B6E6C] text-white' : 'bg-white border border-zinc-200 text-[#111827]' }} text-xs font-semibold px-4 py-2 rounded-full transition">
                    Detail
                </a>
            </div>
        @empty
            <p class="text-xs text-zinc-400">Belum ada kegiatan pada tanggal ini.</p>
        @endforelse
    </section>

    <!-- "Delivery App UI Kit" / Progress Periode & Laporan Card -->
    <section class="bg-white rounded-[28px] p-5 shadow-2xs space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-[16px] font-semibold text-[#111827]">Delivery App UI Kit</h3>
                <p class="text-xs text-zinc-500 mt-1 leading-relaxed">
                    We got a project to make a delivery ui kit called Foodnow • Rekap & Ekspor PDF Bulanan siap diunduh.
                </p>
            </div>
            <span class="w-10 h-10 rounded-[13px] bg-[#C8EBE5] flex items-center justify-center text-[#0B6E6C] shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="3" y="4" width="18" height="16" rx="3"/>
                    <path d="M3 9h18" stroke="#ffffff" stroke-width="2"/>
                </svg>
            </span>
        </div>

        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center gap-1.5">
                <div class="flex -space-x-2">
                    <span class="w-6 h-6 rounded-full bg-[#0B6E6C] text-white border-2 border-white flex items-center justify-center text-[10px] font-bold">E</span>
                    <span class="w-6 h-6 rounded-full bg-[#209CE2] text-white border-2 border-white flex items-center justify-center text-[10px] font-bold">A</span>
                    <span class="w-6 h-6 rounded-full bg-[#F7A61C] text-white border-2 border-white flex items-center justify-center text-[10px] font-bold">R</span>
                </div>
                <span class="text-xs font-semibold text-[#111827]">+3</span>
            </div>
            <span class="text-xs font-bold text-[#0B6E6C]">65%</span>
        </div>

        <!-- Soft Teal Track + Deep KINRA Green Progress Bar -->
        <div class="w-full h-2.5 bg-[#C8EBE5] rounded-full overflow-hidden p-0.5">
            <div class="bg-[#0B6E6C] h-full rounded-full" style="width: 65%"></div>
        </div>
    </section>

    <!-- Filter by Category -->
    <section class="pt-2 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-[#111827]">Semua Entri Logbook ({{ $logbooks->count() }})</h3>
            <div class="flex items-center gap-1">
                <a href="{{ route('logbook.index') }}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ !request('category') ? 'bg-[#0B6E6C] text-white' : 'bg-white text-zinc-600' }}">Semua</a>
                <a href="{{ route('logbook.index', ['category' => 'tugas']) }}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ request('category') === 'tugas' ? 'bg-[#0B6E6C] text-white' : 'bg-white text-zinc-600' }}">Tugas</a>
                <a href="{{ route('logbook.index', ['category' => 'meeting']) }}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ request('category') === 'meeting' ? 'bg-[#0B6E6C] text-white' : 'bg-white text-zinc-600' }}">Meeting</a>
            </div>
        </div>

        @php
            $bgRotation = ['bg-[#C8EBE5]', 'bg-[#D4EEFC]', 'bg-[#FDE9B4]', 'bg-[#FCE1D4]'];
        @endphp
        @foreach ($logbooks as $idx => $log)
            <a href="{{ route('logbook.show', $log) }}"
               class="block {{ $bgRotation[$idx % count($bgRotation)] }} rounded-[24px] p-4 transition hover:translate-y-[-1px]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-700">{{ $log->date->format('d M Y') }} • {{ $log->formatted_duration }}</span>
                    <span class="bg-white px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-[#0B6E6C]">
                        ⚑ {{ $log->category->label() }}
                    </span>
                </div>
                <h4 class="text-sm font-semibold text-[#111827] mt-1">{{ $log->title }}</h4>
                <p class="text-xs text-zinc-700 mt-1 line-clamp-2">{{ $log->final_description }}</p>
            </a>
        @endforeach
    </section>

</div>
@endsection
