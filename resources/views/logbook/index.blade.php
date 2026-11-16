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

        <div class="text-center">
            <h1 class="text-[17px] font-semibold text-[#111827]">
                Kalender Logbook • {{ $currentMonth->format('F Y') }}
            </h1>
            <p class="text-[11px] text-zinc-500">Pilih tanggal untuk melihat atau mencatat kegiatan harian</p>
        </div>

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

    <!-- Catatan Kegiatan Harian pada Tanggal Terpilih (Fokus Logbook, Tanpa Jadwal Meeting) -->
    <section class="bg-white rounded-[28px] p-5 shadow-2xs space-y-3.5">
        <div class="flex items-center justify-between">
            <h2 class="text-[16px] font-semibold text-[#111827]">Catatan Kegiatan Harian</h2>
            <span class="text-xs font-medium text-[#0B6E6C]">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</span>
        </div>

        @forelse ($selectedDayLogs as $idx => $item)
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-8 h-8 rounded-full bg-[#0B6E6C] text-white flex items-center justify-center text-xs font-bold shrink-0">
                        {{ $internship?->participant?->initials ?? 'P' }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-[#111827] truncate">{{ $item->title }}</p>
                        <p class="text-[11px] text-zinc-500 flex items-center gap-1 mt-0.5">
                            <span>🕒</span>
                            <span>{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} ({{ $item->formatted_duration }})</span>
                        </p>
                    </div>
                </div>

                <a href="{{ route('logbook.show', $item) }}"
                   class="shrink-0 {{ $idx === 0 ? 'bg-[#0B6E6C] text-white' : 'bg-[#F4F7F6] text-[#0B6E6C]' }} text-xs font-semibold px-3.5 py-2 rounded-full transition">
                    Lihat Catatan
                </a>
            </div>
        @empty
            <div class="flex items-center justify-between">
                <p class="text-xs text-zinc-400">Belum ada logbook pada tanggal ini.</p>
                <a href="{{ route('logbook.create') }}" class="bg-[#0B6E6C] text-white text-xs font-semibold px-3.5 py-1.5 rounded-full">
                    + Catat Sekarang
                </a>
            </div>
        @endforelse
    </section>

    <!-- Ringkasan Kelengkapan Logbook Bulanan & Pintasan Cetak PDF -->
    <section class="bg-white rounded-[28px] p-5 shadow-2xs space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-[16px] font-semibold text-[#111827]">Rekapitulasi Logbook Bulanan</h3>
                <p class="text-xs text-zinc-500 mt-1 leading-relaxed">
                    Seluruh catatan kegiatan harianmu tersimpan otomatis dan siap dicetak menjadi dokumen Laporan PDF resmi KINRA Sei Mangkei.
                </p>
            </div>
            <a href="{{ route('reports.index') }}"
               title="Cetak Laporan PDF"
               class="w-10 h-10 rounded-[13px] bg-[#C8EBE5] flex items-center justify-center text-[#0B6E6C] shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="3" y="4" width="18" height="16" rx="3"/>
                    <path d="M3 9h18" stroke="#ffffff" stroke-width="2"/>
                </svg>
            </a>
        </div>

        <div class="flex items-center justify-between pt-1">
            <span class="text-xs font-medium text-zinc-600">{{ $logbooks->count() }} Entri Logbook Tercatat</span>
            <a href="{{ route('reports.index') }}" class="text-xs font-bold text-[#0B6E6C] hover:underline">Cetak PDF →</a>
        </div>

        <!-- Soft Teal Track + Deep KINRA Green Progress Bar -->
        <div class="w-full h-2.5 bg-[#C8EBE5] rounded-full overflow-hidden p-0.5">
            <div class="bg-[#0B6E6C] h-full rounded-full" style="width: {{ min(100, max(20, $logbooks->count() * 12)) }}%"></div>
        </div>
    </section>

    <!-- Filter by Category (Fokus Kegiatan Harian, Tanpa istilah Meeting) -->
    <section class="pt-2 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-[#111827]">Daftar Riwayat Logbook ({{ $logbooks->count() }})</h3>
            <div class="flex items-center gap-1">
                <a href="{{ route('logbook.index') }}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ !request('category') ? 'bg-[#0B6E6C] text-white' : 'bg-white text-zinc-600' }}">Semua</a>
                <a href="{{ route('logbook.index', ['category' => 'tugas']) }}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ request('category') === 'tugas' ? 'bg-[#0B6E6C] text-white' : 'bg-white text-zinc-600' }}">Tugas</a>
                <a href="{{ route('logbook.index', ['category' => 'lapangan']) }}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ request('category') === 'lapangan' ? 'bg-[#0B6E6C] text-white' : 'bg-white text-zinc-600' }}">Lapangan</a>
                <a href="{{ route('logbook.index', ['category' => 'administrasi']) }}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ request('category') === 'administrasi' ? 'bg-[#0B6E6C] text-white' : 'bg-white text-zinc-600' }}">Admin</a>
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
