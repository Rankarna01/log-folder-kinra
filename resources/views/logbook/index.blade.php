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
            <i class="fa-solid fa-chevron-left text-xs"></i>
        </a>

        <div class="text-center">
            <h1 class="text-[17px] font-semibold text-[#111827]">
                Kalender Logbook • {{ $currentMonth->format('F Y') }}
            </h1>
        </div>

        <a href="{{ route('logbook.index', ['month' => $nextMonth]) }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0B6E6C] shadow-2xs hover:scale-105 transition">
            <i class="fa-solid fa-chevron-right text-xs"></i>
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

    <!-- Catatan Kegiatan Harian pada Tanggal Terpilih (1 Hari = 1 Logbook) -->
    <section class="bg-white rounded-[28px] p-5 shadow-2xs space-y-3.5">
        <div class="flex items-center justify-between">
            <h2 class="text-[16px] font-semibold text-[#111827]">Catatan Kegiatan Harian</h2>
            <span class="text-xs font-medium text-[#0B6E6C] flex items-center gap-1">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</span>
            </span>
        </div>

        @forelse ($selectedDayLogs as $item)
            <div class="bg-[#F4F7F6] rounded-[20px] p-4 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-9 h-9 rounded-full bg-[#0B6E6C] text-white flex items-center justify-center text-xs font-bold shrink-0">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#111827] truncate">{{ $item->title }}</p>
                            <p class="text-[11px] text-zinc-500 flex items-center gap-1.5 mt-0.5">
                                <i class="fa-regular fa-clock text-[#0B6E6C]"></i>
                                <span>{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} ({{ $item->formatted_duration }})</span>
                                <span>•</span>
                                <i class="{{ $item->location_icon }} text-[#0B6E6C]"></i>
                                <span>{{ $item->location_label }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-zinc-700 leading-relaxed">{{ $item->final_description }}</p>
                <div class="flex items-center gap-2 pt-1">
                    <a href="{{ route('logbook.edit', $item) }}"
                       class="flex-1 bg-[#0B6E6C] text-white text-xs font-semibold py-2 rounded-full text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit</span>
                    </a>
                    <a href="{{ route('logbook.show', $item) }}"
                       class="flex-1 bg-white border border-zinc-200 text-[#111827] text-xs font-semibold py-2 rounded-full text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-eye"></i>
                        <span>Detail</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-between">
                <p class="text-xs text-zinc-400">Belum ada catatan pada tanggal ini.</p>
                <a href="{{ route('logbook.create', ['date' => $selectedDate]) }}"
                   class="bg-[#0B6E6C] hover:bg-[#085856] text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-2xs transition shrink-0">
                    <i class="fa-solid fa-plus text-[9px]"></i>
                    <span>Catat Tanggal</span>
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
                <i class="fa-solid fa-file-pdf text-base"></i>
            </a>
        </div>

        <div class="flex items-center justify-between pt-1">
            <span class="text-xs font-medium text-zinc-600">{{ $logbooks->count() }} Hari Logbook Tercatat</span>
            <a href="{{ route('reports.index') }}" class="text-xs font-bold text-[#0B6E6C] hover:underline flex items-center gap-1">
                <span>Cetak PDF</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="w-full h-2.5 bg-[#C8EBE5] rounded-full overflow-hidden p-0.5">
            <div class="bg-[#0B6E6C] h-full rounded-full" style="width: {{ min(100, max(20, $logbooks->count() * 12)) }}%"></div>
        </div>
    </section>

    <!-- Daftar Riwayat Logbook (Filter Tugas/Lapangan/Admin & Badge Kategori Di-Hide) -->
    <section class="pt-2 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-[#111827]">Daftar Riwayat Logbook ({{ $logbooks->count() }})</h3>
        </div>

        @php
            $bgRotation = ['bg-[#C8EBE5]', 'bg-[#D4EEFC]', 'bg-[#FDE9B4]', 'bg-[#FCE1D4]'];
        @endphp
        @foreach ($logbooks as $idx => $log)
            <a href="{{ route('logbook.show', $log) }}"
               class="block {{ $bgRotation[$idx % count($bgRotation)] }} rounded-[24px] p-4 transition hover:translate-y-[-1px]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-700 flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar text-[#0B6E6C]"></i>
                        <span>{{ $log->date->format('d M Y') }} • {{ $log->formatted_duration }}</span>
                    </span>
                    <span class="bg-white px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-[#0B6E6C] flex items-center gap-1">
                        <i class="{{ $log->location_icon }} text-[10px]"></i>
                        <span>{{ $log->location_label }}</span>
                    </span>
                </div>
                <h4 class="text-sm font-semibold text-[#111827] mt-1.5">{{ $log->title }}</h4>
                <p class="text-xs text-zinc-700 mt-1 line-clamp-2">{{ $log->final_description }}</p>
            </a>
        @endforeach
    </section>

</div>
@endsection
