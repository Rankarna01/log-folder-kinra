@extends('layouts.app')

@section('content')
<div class="px-5 pt-4 pb-8 space-y-4">

    <!-- Back Navigation + Quick Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('logbook.index') }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-xs hover:scale-105 transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

        <div class="flex items-center gap-2">
            @if ($logbook->status->value === 'draft' || $logbook->status->value === 'revision')
                <form action="{{ route('logbook.submit', $logbook) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-[#18181B] text-white text-xs font-semibold px-4 py-2 rounded-full shadow-sm">
                        Kirim ke Mentor →
                    </button>
                </form>
            @endif
            <form action="{{ route('logbook.duplicate', $logbook) }}" method="POST">
                @csrf
                <button type="submit" title="Duplikat Entri" class="bg-white text-[#18181B] text-xs font-semibold px-3 py-2 rounded-full shadow-xs">
                    Salin
                </button>
            </form>
        </div>
    </div>

    <!-- Header Detail Card in Lavender Grid (Exact Screen 2 Top Card in style-design.png) -->
    <section class="bg-lavender-grid rounded-[28px] p-5 shadow-xs">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-[14px] bg-white flex items-center justify-center text-[#18181B] shadow-2xs">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="3" y="4" width="18" height="16" rx="3"/>
                    <path d="M3 9h18" stroke="#ffffff" stroke-width="2"/>
                </svg>
            </div>
            <span class="bg-white px-3 py-1 rounded-full text-xs font-semibold text-[#18181B]">
                ⚑ {{ $logbook->status->label() }} • {{ strtoupper($logbook->work_location ?? 'WFO') }}
            </span>
        </div>

        <h1 class="text-[22px] font-semibold text-[#111827] mt-4 leading-snug">
            {{ $logbook->title }}
        </h1>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-zinc-600">Created by</span>
                <span class="w-6 h-6 rounded-full bg-[#F8C8C4] border border-white inline-flex items-center justify-center text-[10px] font-bold text-[#18181B]">
                    {{ $logbook->internship?->participant?->initials ?? 'EH' }}
                </span>
                <span class="text-xs font-semibold text-[#111827]">
                    {{ $logbook->internship?->participant?->name ?? 'Esther Howard' }}
                </span>
            </div>

            @if ($logbook->isEditable())
                <a href="{{ route('logbook.edit', $logbook) }}"
                   title="Edit Logbook"
                   class="w-9 h-9 rounded-full bg-white/70 hover:bg-white flex items-center justify-center text-[#18181B] transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z"/>
                    </svg>
                </a>
            @endif
        </div>
    </section>

    <!-- 2-Column Bento Info Grid: Deadline & People (Exact Screen 2 Middle Cards) -->
    <section class="grid grid-cols-2 gap-3.5">
        <!-- Left Card: Deadline / Tanggal & Durasi -->
        <div class="bg-white rounded-[28px] p-4 text-center shadow-2xs flex flex-col items-center justify-between">
            <span class="text-xs text-zinc-400 font-medium">Deadline / Tanggal</span>
            <div class="my-2.5 w-10 h-10 rounded-[12px] bg-[#18181B] text-white flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="17" rx="3"/>
                    <path d="M8 2v4M16 2v4M3 9h18"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-[#111827]">{{ $logbook->date->format('d F') }}</p>
                <p class="text-[11px] text-zinc-400">{{ substr($logbook->start_time, 0, 5) }} - {{ substr($logbook->end_time, 0, 5) }} ({{ $logbook->formatted_duration }})</p>
            </div>
        </div>

        <!-- Right Card: People / Mentor & Tim -->
        <div class="bg-white rounded-[28px] p-4 text-center shadow-2xs flex flex-col items-center justify-between">
            <span class="text-xs text-zinc-400 font-medium">People</span>
            <div class="my-2 flex items-center justify-center -space-x-2.5">
                <div class="w-10 h-10 rounded-full bg-[#D5EDE8] border-2 border-white flex items-center justify-center text-xs font-bold text-[#18181B]">
                    AN
                </div>
                <div class="w-12 h-12 rounded-full bg-[#F8C8C4] border-2 border-white flex items-center justify-center text-sm font-bold text-[#18181B] shadow-xs">
                    {{ $logbook->internship?->participant?->initials ?? 'EH' }}
                </div>
                <div class="w-10 h-10 rounded-full bg-[#E5D9FA] border-2 border-white flex items-center justify-center text-xs font-bold text-[#18181B]">
                    AI
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold text-[#111827]">Mentor: {{ $logbook->internship?->mentor?->name ?? 'Agnes Nielsen' }}</p>
                <p class="text-[11px] text-zinc-400">Skor Kualitas: {{ $quality['score'] }}/100</p>
            </div>
        </div>
    </section>

    <!-- Segmented Pill Tabs: Goals (Rincian & Sub-output) | Chat (Diskusi Mentor) -->
    <div class="bg-[#ECEEF3] p-1.5 rounded-full grid grid-cols-2 gap-1">
        <button type="button"
                id="tab-btn-goals"
                onclick="switchDetailTab('goals')"
                class="bg-white text-[#111827] font-semibold text-sm py-2.5 rounded-full shadow-xs transition">
            Goals & Output
        </button>
        <button type="button"
                id="tab-btn-chat"
                onclick="switchDetailTab('chat')"
                class="text-zinc-500 font-medium text-sm py-2.5 rounded-full transition">
            Chat ({{ $logbook->comments->count() }})
        </button>
    </div>

    <!-- TAB 1: Goals & Sub-Items Breakdown (Exact Screen 2 White Sub-Cards) -->
    <div id="tab-panel-goals" class="space-y-3">
        @php
            $subItems = collect(preg_split('/[•,\n]+/', (string) $logbook->output))
                ->map(fn ($s) => trim($s))
                ->filter()
                ->values();

            if ($subItems->isEmpty()) {
                $subItems = collect(['Design system', 'Landing Page', 'Pricing Page', 'Copywriting']);
            }
        @endphp

        @foreach ($subItems as $idx => $itemTitle)
            @php $isLow = $idx === 2; @endphp
            <div class="bg-white rounded-[26px] p-4 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3.5 min-w-0">
                    <span class="w-9 h-9 rounded-full bg-[#D5EDE8] text-[#2D8A78] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h4 class="text-[15px] font-semibold text-[#111827] truncate">{{ $itemTitle }}</h4>
                        <p class="text-xs text-zinc-400 truncate">{{ $logbook->category->label() }} • Charty App</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="{{ $isLow ? 'bg-[#D5EDE8] text-emerald-900' : 'bg-[#E5D9FA] text-purple-950' }} px-3 py-1 rounded-full text-xs font-semibold">
                        ⚑ {{ $isLow ? 'Low' : 'High' }}
                    </span>
                    <span class="text-zinc-400 px-1">⋮</span>
                </div>
            </div>
        @endforeach

        <!-- Final Description vs Raw Text Card -->
        <div class="bg-white rounded-[26px] p-5 space-y-3 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Deskripsi Laporan Final</span>
                @if ($logbook->ai_description)
                    <span class="bg-[#E5D9FA] text-[#18181B] text-[11px] font-semibold px-2.5 py-0.5 rounded-full">
                        ✨ Dirapikan AI Gemini
                    </span>
                @endif
            </div>
            <p class="text-sm text-[#111827] leading-relaxed">
                {{ $logbook->final_description }}
            </p>

            @if ($logbook->raw_description && $logbook->raw_description !== $logbook->final_description)
                <div class="pt-3 border-t border-zinc-100">
                    <p class="text-[11px] font-semibold text-zinc-400">Catatan Kasar Asli Peserta (Tersimpan Aman):</p>
                    <p class="text-xs text-zinc-500 italic mt-0.5">"{{ $logbook->raw_description }}"</p>
                </div>
            @endif

            @if ($logbook->obstacles)
                <div class="pt-2 border-t border-zinc-100">
                    <p class="text-[11px] font-semibold text-zinc-400">Kendala Lapangan:</p>
                    <p class="text-xs text-zinc-700 mt-0.5">{{ $logbook->obstacles }}</p>
                </div>
            @endif
        </div>

        <!-- Mentor Quick Review Controls -->
        <div class="bg-[#D5EDE8] rounded-[26px] p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#111827]">Panel Persetujuan Mentor</span>
                <span class="text-[11px] text-zinc-600">{{ $logbook->internship?->mentor?->name ?? 'Agnes Nielsen' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('review.approve', $logbook) }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="message" value="Logbook disetujui, dokumentasi dan output sesuai target.">
                    <button type="submit" class="w-full bg-[#18181B] text-white text-xs font-semibold py-2.5 rounded-full shadow-xs">
                        ✓ Approve Logbook
                    </button>
                </form>
                <button type="button"
                        onclick="switchDetailTab('chat')"
                        class="flex-1 bg-white text-[#18181B] text-xs font-semibold py-2.5 rounded-full shadow-xs">
                    ✎ Minta Revisi
                </button>
            </div>
        </div>
    </div>

    <!-- TAB 2: Chat & Mentor Review Comments -->
    <div id="tab-panel-chat" class="hidden space-y-3">
        @forelse ($logbook->comments as $comment)
            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-[#E5D9FA] flex items-center justify-center text-xs font-bold">
                            {{ $comment->user?->initials ?? 'M' }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-[#111827]">{{ $comment->user?->name ?? 'Mentor' }}</p>
                            <p class="text-[10px] text-zinc-400">{{ $comment->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $comment->type === 'revision_request' ? 'bg-[#FBE8A6] text-amber-900' : 'bg-[#D5EDE8] text-emerald-900' }}">
                        {{ $comment->type === 'revision_request' ? 'Permintaan Revisi' : 'Diskusi / Approval' }}
                    </span>
                </div>
                <p class="text-xs text-zinc-700 mt-2.5 leading-relaxed">{{ $comment->message }}</p>
            </div>
        @empty
            <div class="bg-white rounded-[24px] p-6 text-center text-xs text-zinc-400">
                Belum ada komentar pada entri ini.
            </div>
        @endforelse

        <!-- Add Comment / Request Revision Form -->
        <form action="{{ route('review.comment', $logbook) }}" method="POST" class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
            @csrf
            <label class="block text-xs font-semibold text-[#111827]">Tambah Komentar / Catatan Revisi</label>
            <textarea name="message" rows="3" required
                      placeholder="Tulis umpan balik atau balasan diskusi..."
                      class="w-full rounded-[16px] bg-[#F6F7F9] border-0 p-3 text-xs text-[#111827] focus:ring-2 focus:ring-[#18181B]"></textarea>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-[#18181B] text-white text-xs font-semibold py-2.5 rounded-full">
                    Kirim Komentar
                </button>
                <button type="submit"
                        formaction="{{ route('review.revision', $logbook) }}"
                        class="bg-[#FBE8A6] text-[#18181B] text-xs font-semibold px-4 py-2.5 rounded-full">
                    Tandai Revisi
                </button>
            </div>
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
            chatBtn.className = 'bg-white text-[#111827] font-semibold text-sm py-2.5 rounded-full shadow-xs transition';
            goalsBtn.className = 'text-zinc-500 font-medium text-sm py-2.5 rounded-full transition';
        } else {
            chatPanel.classList.add('hidden');
            goalsPanel.classList.remove('hidden');
            goalsBtn.className = 'bg-white text-[#111827] font-semibold text-sm py-2.5 rounded-full shadow-xs transition';
            chatBtn.className = 'text-zinc-500 font-medium text-sm py-2.5 rounded-full transition';
        }
    }

    if (window.location.hash === '#comments') {
        switchDetailTab('chat');
    }
</script>
@endsection
