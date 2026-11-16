@extends('layouts.app')

@section('content')
<div class="px-5 pt-4 pb-8 space-y-4">

    <!-- Back Navigation + Edit Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('logbook.index') }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-xs hover:scale-105 transition">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('logbook.edit', $logbook) }}"
               class="bg-[#0B6E6C] text-white text-xs font-semibold px-4 py-2 rounded-full shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Logbook</span>
            </a>
        </div>
    </div>

    <!-- Header Detail Card in KINRA Deep Green Grid (Location Badge Only, Category Hidden) -->
    <section class="bg-kinra-grid rounded-[28px] p-5 shadow-xs text-white">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-[14px] bg-white flex items-center justify-center text-[#0B6E6C] shadow-2xs">
                <i class="fa-solid fa-book-open text-base"></i>
            </div>
            <span class="bg-white px-3 py-1 rounded-full text-xs font-semibold text-[#0B6E6C] flex items-center gap-1.5">
                <i class="{{ $logbook->location_icon }}"></i>
                <span>Lokasi: {{ $logbook->location_label }}</span>
            </span>
        </div>

        <h1 class="text-[22px] font-semibold text-white mt-4 leading-snug">
            {{ $logbook->title }}
        </h1>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-emerald-100">Dicatat oleh</span>
                <span class="w-6 h-6 rounded-full bg-[#C8EBE5] border border-white inline-flex items-center justify-center text-[10px] font-bold text-[#074745]">
                    {{ $logbook->internship?->participant?->initials ?? 'P' }}
                </span>
                <span class="text-xs font-semibold text-white">
                    {{ $logbook->internship?->participant?->name ?? 'Peserta Magang' }}
                </span>
            </div>

            <a href="{{ route('logbook.edit', $logbook) }}"
               title="Edit Logbook"
               class="w-9 h-9 rounded-full bg-white/85 hover:bg-white flex items-center justify-center text-[#0B6E6C] transition">
                <i class="fa-solid fa-pen text-xs"></i>
            </a>
        </div>
    </section>

    <!-- 2-Column Bento Info Grid: Tanggal/Durasi & Divisi/Mentor -->
    <section class="grid grid-cols-2 gap-3.5">
        <div class="bg-white rounded-[28px] p-4 text-center shadow-2xs flex flex-col items-center justify-between">
            <span class="text-xs text-zinc-400 font-medium">Tanggal & Jam Kegiatan</span>
            <div class="my-2.5 w-10 h-10 rounded-[12px] bg-[#0B6E6C] text-white flex items-center justify-center">
                <i class="fa-regular fa-calendar-check text-base"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-[#111827]">{{ $logbook->date->format('d F Y') }}</p>
                <p class="text-[11px] text-zinc-400">{{ substr($logbook->start_time, 0, 5) }} - {{ substr($logbook->end_time, 0, 5) }} ({{ $logbook->formatted_duration }})</p>
            </div>
        </div>

        <div class="bg-white rounded-[28px] p-4 text-center shadow-2xs flex flex-col items-center justify-between">
            <span class="text-xs text-zinc-400 font-medium">Divisi & Pembimbing</span>
            <div class="my-2 flex items-center justify-center -space-x-2.5">
                <div class="w-10 h-10 rounded-full bg-[#C8EBE5] border-2 border-white flex items-center justify-center text-xs font-bold text-[#074745]">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#0B6E6C] border-2 border-white flex items-center justify-center text-sm font-bold text-white shadow-xs">
                    {{ $logbook->internship?->participant?->initials ?? 'P' }}
                </div>
                <div class="w-10 h-10 rounded-full bg-[#D4EEFC] border-2 border-white flex items-center justify-center text-xs font-bold text-[#0C4A6E]">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold text-[#111827] truncate">{{ $logbook->internship?->division ?? 'Divisi Magang' }}</p>
                <p class="text-[11px] text-zinc-400 truncate">Mentor: {{ $logbook->internship?->mentor?->name ?? '-' }}</p>
            </div>
        </div>
    </section>

    <!-- Segmented Pill Tabs: Rincian & Hasil Kerja | Catatan Tambahan -->
    <div class="bg-[#E6ECEB] p-1.5 rounded-full grid grid-cols-2 gap-1">
        <button type="button"
                id="tab-btn-goals"
                onclick="switchDetailTab('goals')"
                class="bg-white text-[#0B6E6C] font-semibold text-sm py-2.5 rounded-full shadow-xs transition">
            Rincian & Hasil Kerja
        </button>
        <button type="button"
                id="tab-btn-chat"
                onclick="switchDetailTab('chat')"
                class="text-zinc-500 font-medium text-sm py-2.5 rounded-full transition">
            Catatan Tambahan ({{ $logbook->comments->count() }})
        </button>
    </div>

    <!-- TAB 1: Rincian Kegiatan & Output -->
    <div id="tab-panel-goals" class="space-y-3">
        @php
            $subItems = collect(preg_split('/[•,\n]+/', (string) $logbook->output))
                ->map(fn ($s) => trim($s))
                ->filter()
                ->values();

            if ($subItems->isEmpty()) {
                $subItems = collect(['Pencatatan Kegiatan Harian', 'Dokumentasi Hasil Pekerjaan', 'Arsip Dokumen Divisi']);
            }
        @endphp

        @foreach ($subItems as $itemTitle)
            <div class="bg-white rounded-[26px] p-4 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3.5 min-w-0">
                    <span class="w-9 h-9 rounded-full bg-[#C8EBE5] text-[#0B6E6C] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-sm"></i>
                    </span>
                    <div class="min-w-0">
                        <h4 class="text-[15px] font-semibold text-[#111827] truncate">{{ $itemTitle }}</h4>
                        <p class="text-xs text-zinc-400 truncate">Output Kegiatan • {{ $logbook->location_label }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="bg-[#C8EBE5] text-[#074745] px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                        <span>Selesai</span>
                    </span>
                </div>
            </div>
        @endforeach

        <!-- Final Description Card -->
        <div class="bg-white rounded-[26px] p-5 space-y-3 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Uraian Logbook Harian</span>
                <span class="bg-[#C8EBE5] text-[#074745] text-[11px] font-semibold px-2.5 py-0.5 rounded-full flex items-center gap-1">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    <span>Tersimpan</span>
                </span>
            </div>
            <p class="text-sm text-[#111827] leading-relaxed">
                {{ $logbook->final_description }}
            </p>

            @if ($logbook->obstacles)
                <div class="pt-2 border-t border-zinc-100">
                    <p class="text-[11px] font-semibold text-zinc-400">Kendala:</p>
                    <p class="text-xs text-zinc-700 mt-0.5">{{ $logbook->obstacles }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Personal Notes & Documentation -->
    <div id="tab-panel-chat" class="hidden space-y-3">
        @forelse ($logbook->comments as $comment)
            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-[#0B6E6C] text-white flex items-center justify-center text-xs font-bold">
                            {{ $comment->user?->initials ?? 'P' }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-[#111827]">{{ $comment->user?->name ?? 'Catatan' }}</p>
                            <p class="text-[10px] text-zinc-400">{{ $comment->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-zinc-700 mt-2.5 leading-relaxed">{{ $comment->message }}</p>
            </div>
        @empty
            <div class="bg-white rounded-[24px] p-6 text-center text-xs text-zinc-400">
                Belum ada catatan tambahan pada logbook ini.
            </div>
        @endforelse

        <!-- Add Note Form -->
        <form action="{{ route('review.comment', $logbook) }}" method="POST" class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
            @csrf
            <label class="block text-xs font-semibold text-[#111827]">Tambah Catatan Logbook</label>
            <textarea name="message" rows="3" required
                      placeholder="Tulis catatan tambahan terkait kegiatan harian ini..."
                      class="w-full rounded-[16px] bg-[#F4F7F6] border-0 p-3 text-xs text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]"></textarea>
            <button type="submit" class="w-full bg-[#0B6E6C] text-white text-xs font-semibold py-2.5 rounded-full flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Catatan</span>
            </button>
        </form>
    </div>

</div>

<script>
    function switchDetailTab(tab) {
        const goalsPanel = document.getElementById('tab-panel-goals');
        const chatPanel = document.getElementById('tab-panel-chat');
        const goalsBtn = document.getElementById('tab-btn-goals');
        const chatBtn = document.getElementById('tab-btn-chat');

        if (tab === 'chat') {
            goalsPanel.classList.add('hidden');
            chatPanel.classList.remove('hidden');
            chatBtn.className = 'bg-white text-[#0B6E6C] font-semibold text-sm py-2.5 rounded-full shadow-xs transition';
            goalsBtn.className = 'text-zinc-500 font-medium text-sm py-2.5 rounded-full transition';
        } else {
            chatPanel.classList.add('hidden');
            goalsPanel.classList.remove('hidden');
            goalsBtn.className = 'bg-white text-[#0B6E6C] font-semibold text-sm py-2.5 rounded-full shadow-xs transition';
            chatBtn.className = 'text-zinc-500 font-medium text-sm py-2.5 rounded-full transition';
        }
    }

    if (window.location.hash === '#comments') {
        switchDetailTab('chat');
    }
</script>
@endsection
