@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-6 space-y-5">

    <!-- Top App Bar (Green Avatar + KINRA Logo + Font Awesome Action Buttons) -->
    <header class="flex items-center justify-between gap-2">
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 group min-w-0">
            <div class="w-11 h-11 rounded-full bg-[#0B6E6C] border-2 border-white shadow-sm flex items-center justify-center text-white shrink-0">
                <i class="fa-solid fa-user-tie text-base"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-[15px] font-semibold text-[#111827] group-hover:underline truncate">{{ $user->name }}</h1>
                <p class="text-[11px] text-zinc-500 truncate">
                    {{ $internship?->division ?? 'Divisi Umum' }} • {{ $internship?->position ?? 'Peserta Magang' }}
                </p>
            </div>
        </a>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Official KINRA Sei Mangkei Logo Pill -->
            <div class="bg-white rounded-full px-2.5 py-1.5 shadow-sm flex items-center justify-center">
                <img src="/images/kinra.png" alt="KINRA Sei Mangkei" class="h-5 w-auto object-contain">
            </div>
        </div>
    </header>

    <!-- Hero KINRA Deep Green Grid Card: Rekap Pencatatan Logbook Harian -->
    <section class="bg-kinra-grid rounded-[28px] p-5 relative overflow-hidden shadow-sm text-white">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <h2 class="text-[14px] font-bold text-white leading-tight">
                    <span class="block whitespace-nowrap">Pencatatan Logbook</span>
                    <span class="block whitespace-nowrap">Kegiatan Harian</span>
                </h2>
                <p class="text-xs text-emerald-100 mt-1.5 font-medium truncate">
                    Mentor: {{ $internship?->mentor?->name ?? '-' }}
                </p>
            </div>
            @if (! $todayLogbook)
                <a href="{{ route('logbook.create') }}"
                   class="bg-white hover:bg-[#FDE9B4] text-[#0B6E6C] text-[11px] font-bold px-3.5 py-1.5 rounded-full shadow-sm transition shrink-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Catat Hari Ini</span>
                </a>
            @else
                <a href="{{ route('logbook.edit', $todayLogbook) }}"
                   class="bg-[#FDE9B4] hover:bg-white text-[#18181B] text-[11px] font-bold px-3.5 py-1.5 rounded-full shadow-sm transition shrink-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                    <span>Edit Hari Ini</span>
                </a>
            @endif
        </div>

        <!-- Dark Charcoal Inner Live Clock Card -->
        <div class="mt-5 bg-[#18181B] rounded-[22px] px-5 py-4 flex items-center justify-between relative">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#FDE9B4]">Waktu Saat Ini</span>
                </div>
                <div id="live-current-time" class="text-[24px] font-bold text-white tracking-tight leading-tight mt-0.5 tabular-nums">
                    {{ now()->timezone('Asia/Jakarta')->format('H:i:s') }} WIB
                </div>
                <p id="live-current-date" class="text-xs text-zinc-400 mt-1">
                    {{ now()->timezone('Asia/Jakarta')->translatedFormat('l, d M Y') }} • {{ $logbooks->count() }} hari tercatat
                </p>
            </div>

            <!-- Modern Clock Icon -->
            <div class="w-16 h-16 rounded-[20px] bg-[#F7A61C] text-[#18181B] flex items-center justify-center shadow-lg shrink-0">
                <i class="fa-solid fa-clock-rotate-left text-3xl"></i>
            </div>
        </div>
    </section>

    <script>
        (function () {
            const timeEl = document.getElementById('live-current-time');
            const dateEl = document.getElementById('live-current-date');
            const logCount = @js($logbooks->count());
            const daysId = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const monthsId = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            function tickClock() {
                const now = new Date();
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                if (timeEl) {
                    timeEl.textContent = `${hh}:${mm}:${ss} WIB`;
                }
                if (dateEl) {
                    const dayName = daysId[now.getDay()];
                    const d = String(now.getDate()).padStart(2, '0');
                    const m = monthsId[now.getMonth()];
                    const y = now.getFullYear();
                    dateEl.textContent = `${dayName}, ${d} ${m} ${y} • ${logCount} hari tercatat`;
                }
            }

            tickClock();
            setInterval(tickClock, 1000);
        })();
    </script>

    <!-- Quick Action Pills (Create button hidden if today's logbook already exists) -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-0.5">
        @if (! $todayLogbook)
            <a href="{{ route('logbook.create') }}"
               class="shrink-0 bg-[#18181B] text-white text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <i class="fa-solid fa-circle-plus"></i>
                <span>Tambah Logbook</span>
            </a>
        @else
            <a href="{{ route('logbook.create') }}"
               class="shrink-0 bg-[#0B6E6C] text-white text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <i class="fa-solid fa-circle-check"></i>
                <span>Logbook Hari Ini</span>
            </a>
        @endif

        <a href="{{ route('logbook.create') }}"
           class="shrink-0 bg-[#C8EBE5] hover:bg-[#B3E2DA] text-[#074745] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <i class="fa-solid fa-microphone-lines"></i>
            <span>Voice Suara</span>
        </a>

        <button type="button"
                onclick="openAdvancedFeatureModal('ai')"
                class="shrink-0 bg-[#D4EEFC] hover:bg-[#BCE3FA] text-[#0C4A6E] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>Asisten AI</span>
            <span class="bg-[#209CE2] text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">Lanjutan</span>
        </button>

        <a href="{{ route('reports.index') }}"
           class="shrink-0 bg-[#FDE9B4] hover:bg-[#FBD97F] text-[#78350F] text-xs font-semibold px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
            <i class="fa-solid fa-file-pdf"></i>
            <span>Cetak PDF</span>
        </a>
    </div>

    <!-- Section Header: "Catatan Logbook (3 Hari Terakhir)" + "Lihat Semua" -->
    <div class="flex items-center justify-between pt-1">
        <h3 class="text-[17px] font-semibold text-[#111827]">Catatan Logbook (3 Hari Terakhir)</h3>
        <a href="{{ route('logbook.index') }}" class="text-xs font-semibold text-[#0B6E6C] hover:underline flex items-center gap-1">
            <span>Lihat Semua</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <!-- Colored Logbook Cards (Max 3 Recent Days, No Category Badge, Shows Kantor/Lapangan Location) -->
    @php
        $cardPalettes = [
            ['bg' => 'bg-[#C8EBE5]', 'bar' => 'bg-[#0B6E6C]', 'pct' => 100],
            ['bg' => 'bg-[#D4EEFC]', 'bar' => 'bg-[#209CE2]', 'pct' => 100],
            ['bg' => 'bg-[#FDE9B4]', 'bar' => 'bg-[#F7A61C]', 'pct' => 100],
        ];
    @endphp

    <div class="space-y-4">
        @forelse ($recentThreeDaysLogs as $index => $log)
            @php
                $palette = $cardPalettes[$index % count($cardPalettes)];
            @endphp
            <article class="{{ $palette['bg'] }} rounded-[28px] p-4 transition hover:translate-y-[-2px] shadow-sm">
                <!-- Row 1: White Squircle Icon + Title + Edit Pencil Circle -->
                <div class="flex items-center justify-between gap-2">
                    <a href="{{ route('logbook.show', $log) }}" class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-[12px] bg-white flex items-center justify-center text-[#0B6E6C] shrink-0 shadow-xs">
                            <i class="fa-solid fa-book-open text-sm"></i>
                        </span>
                        <h4 class="text-[15px] font-semibold text-[#111827] truncate">
                            {{ $log->title }}
                        </h4>
                    </a>

                    <a href="{{ route('logbook.edit', $log) }}"
                       title="Edit Logbook"
                       class="w-8 h-8 rounded-full bg-white/70 hover:bg-white flex items-center justify-center text-[#18181B] shrink-0 transition">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                </div>

                <!-- Row 2: Jam Kegiatan + Lokasi Kegiatan Pill (Kantor / Lapangan) -->
                <div class="flex items-center gap-2.5 mt-3">
                    <span class="inline-flex items-center gap-1.5 text-xs text-zinc-700 font-medium">
                        <i class="fa-regular fa-clock text-[#0B6E6C]"></i>
                        <span>{{ substr($log->start_time, 0, 5) }} - {{ substr($log->end_time, 0, 5) }} ({{ $log->formatted_duration }})</span>
                    </span>

                    <span class="inline-flex items-center gap-1.5 bg-white px-3 py-1 rounded-full text-xs font-semibold text-[#0B6E6C] shadow-2xs">
                        <i class="{{ $log->location_icon }} text-[11px]"></i>
                        <span>{{ $log->location_label }}</span>
                    </span>
                </div>

                <!-- Description Preview -->
                <p class="mt-2.5 text-xs text-zinc-700 line-clamp-2 leading-relaxed">
                    {{ $log->final_description }}
                </p>

                <!-- Row 3: White Progress Bar Track + KINRA Tonal Fill -->
                <div class="mt-3 w-full h-2 bg-white/85 rounded-full overflow-hidden p-0.5">
                    <div class="{{ $palette['bar'] }} h-full rounded-full transition-all"
                         style="width: {{ $palette['pct'] }}%"></div>
                </div>

                <!-- Row 4: Peserta | Tanggal Logbook | Detail Link -->
                <div class="mt-3.5 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded-full bg-[#0B6E6C] text-white border-2 border-white flex items-center justify-center text-[10px] font-bold">
                            {{ $user->initials }}
                        </span>
                        <span class="text-xs font-semibold text-[#111827]">{{ $log->location_label }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <!-- White Date Pill -->
                        <a href="{{ route('logbook.show', $log) }}"
                           class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full text-xs font-semibold text-[#111827] shadow-2xs">
                            <i class="fa-regular fa-calendar text-[#0B6E6C]"></i>
                            <span>{{ $log->date->format('d M Y') }}</span>
                        </a>

                        <!-- Detail Logbook Pill -->
                        <a href="{{ route('logbook.show', $log) }}"
                           class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full text-xs font-semibold text-[#0B6E6C] shadow-2xs">
                            <span>Detail</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white rounded-[28px] p-8 text-center">
                <p class="text-sm font-semibold text-zinc-700">Belum ada logbook harian tercatat.</p>
                <a href="{{ route('logbook.create') }}" class="mt-3 inline-flex items-center gap-2 bg-[#0B6E6C] hover:bg-[#085856] text-white text-xs font-semibold px-5 py-2.5 rounded-full shadow-xs transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Logbook Harian</span>
                </a>
            </div>
        @endforelse
    </div>

    <!-- Desktop only: tombol tambah logbook di bawah catatan 3 hari terakhir -->
    @unless ($todayLogbook)
        <div class="hidden lg:flex justify-center pt-2">
            <a href="{{ route('logbook.create') }}"
               class="inline-flex items-center gap-3 rounded-full border-[2.5px] border-[#18181B] bg-white px-7 py-3 text-sm font-bold text-[#18181B] shadow-[0_10px_24px_rgba(24,24,27,0.12)] transition hover:bg-[#0B6E6C] hover:text-white">
                <i class="fa-solid fa-circle-plus"></i>
                <span>Tambah Logbook</span>
            </a>
        </div>
    @endunless
</div>
@endsection
