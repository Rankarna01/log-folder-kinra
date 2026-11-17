@extends('layouts.app')

@section('content')
<div class="px-5 pt-4 pb-8 space-y-4">

    <header class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-2xs">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </a>
        <h1 class="text-[17px] font-semibold text-[#111827]">
            {{ $existingTodayLog ? 'Logbook Hari Ini' : 'Tambah Logbook Harian' }}
        </h1>
        <button type="button"
                onclick="openAdvancedFeatureModal('ai')"
                class="bg-[#0B6E6C] hover:bg-[#085856] text-white text-[11px] font-bold px-3 py-1.5 rounded-full transition flex items-center gap-1.5">
            <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i>
            <span>Fitur AI</span>
        </button>
    </header>

    <!-- Active Participant Info Banner -->
    @if ($internship)
        <div class="bg-[#C8EBE5] rounded-[22px] px-4 py-3 flex items-center justify-between text-xs">
            <div class="min-w-0">
                <p class="font-bold text-[#074745] truncate">
                    {{ auth()->user()?->name ?? $internship->participant?->name }} • {{ $internship->division }}
                </p>
                <p class="text-[11px] text-zinc-700 truncate">
                    Bagian: {{ $internship->position ?? '-' }} • Mentor: {{ $internship->mentor?->name ?? '-' }}
                </p>
            </div>
            <a href="{{ route('profile.show') }}" class="bg-white text-[#0B6E6C] text-[11px] font-bold px-3 py-1 rounded-full shrink-0 ml-2 shadow-2xs">
                Ubah Data
            </a>
        </div>
    @endif

    @if ($existingTodayLog)
        <!-- 1 DAY = 1 LOGBOOK: Form is hidden, displays recorded data as Bento Grid Cards + Edit button -->
        <section class="bg-kinra-grid rounded-[28px] p-5 text-white shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 bg-white text-[#0B6E6C] text-[11px] font-bold px-3 py-1 rounded-full">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Sudah Tercatat Hari Ini</span>
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full">
                    <i class="{{ $existingTodayLog->location_icon }}"></i>
                    <span>{{ $existingTodayLog->location_label }}</span>
                </span>
            </div>

            <h2 class="text-[20px] font-bold text-white leading-snug pt-1">
                {{ $existingTodayLog->title }}
            </h2>
            <p class="text-xs text-emerald-100">
                Pencatatan dibatasi 1 logbook per hari. Kamu tetap dapat mengubah isi catatan hari ini melalui tombol Edit di bawah.
            </p>
        </section>

        <!-- Bento Grid Cards of Today's Recorded Logbook -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-[24px] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-zinc-400">Tanggal Kegiatan</span>
                    <span class="w-8 h-8 rounded-[10px] bg-[#C8EBE5] text-[#0B6E6C] flex items-center justify-center">
                        <i class="fa-regular fa-calendar-check text-sm"></i>
                    </span>
                </div>
                <p class="text-sm font-bold text-[#111827] mt-2">
                    {{ $existingTodayLog->date->format('d M Y') }}
                </p>
            </div>

            <div class="bg-white rounded-[24px] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-zinc-400">Jam & Durasi</span>
                    <span class="w-8 h-8 rounded-[10px] bg-[#D4EEFC] text-[#0C4A6E] flex items-center justify-center">
                        <i class="fa-regular fa-clock text-sm"></i>
                    </span>
                </div>
                <p class="text-sm font-bold text-[#111827] mt-2">
                    {{ substr($existingTodayLog->start_time, 0, 5) }} - {{ substr($existingTodayLog->end_time, 0, 5) }}
                    <span class="text-xs font-medium text-zinc-500">({{ $existingTodayLog->formatted_duration }})</span>
                </p>
            </div>

            <div class="bg-white rounded-[24px] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-zinc-400">Lokasi Kegiatan</span>
                    <span class="w-8 h-8 rounded-[10px] bg-[#FDE9B4] text-[#78350F] flex items-center justify-center">
                        <i class="{{ $existingTodayLog->location_icon }} text-sm"></i>
                    </span>
                </div>
                <p class="text-sm font-bold text-[#111827] mt-2">
                    {{ $existingTodayLog->location_label }}
                </p>
            </div>

            <div class="bg-white rounded-[24px] p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-zinc-400">Pembimbing</span>
                    <span class="w-8 h-8 rounded-[10px] bg-[#C8EBE5] text-[#0B6E6C] flex items-center justify-center">
                        <i class="fa-solid fa-user-tie text-sm"></i>
                    </span>
                </div>
                <p class="text-sm font-bold text-[#111827] mt-2 truncate">
                    {{ $internship?->mentor?->name ?? '-' }}
                </p>
            </div>
        </div>

        <!-- Description & Output Bento Card -->
        <div class="bg-white rounded-[26px] p-5 space-y-3 shadow-2xs">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Uraian Kegiatan Harian</p>
                <p class="text-xs text-[#111827] leading-relaxed mt-1">
                    {{ $existingTodayLog->final_description }}
                </p>
            </div>

            @if ($existingTodayLog->output)
                <div class="pt-2.5 border-t border-zinc-100">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Hasil / Output</p>
                    <p class="text-xs font-semibold text-[#0B6E6C] mt-1">{{ $existingTodayLog->output }}</p>
                </div>
            @endif

            @if ($existingTodayLog->obstacles)
                <div class="pt-2.5 border-t border-zinc-100">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Kendala</p>
                    <p class="text-xs text-zinc-700 mt-1">{{ $existingTodayLog->obstacles }}</p>
                </div>
            @endif
        </div>

        <!-- Edit & Detail Action Buttons -->
        <div class="grid grid-cols-2 gap-3 pt-1">
            <a href="{{ route('logbook.edit', $existingTodayLog) }}"
               class="bg-[#0B6E6C] hover:bg-[#085856] text-white text-xs font-bold py-3.5 rounded-full shadow-md flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Logbook Ini</span>
            </a>
            <a href="{{ route('logbook.show', $existingTodayLog) }}"
               class="bg-white border-2 border-[#18181B] text-[#18181B] text-xs font-bold py-3.5 rounded-full flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-eye"></i>
                <span>Lihat Detail</span>
            </a>
        </div>
    @else
        <!-- CREATE FORM (Shown only when no logbook exists yet for the selected date) -->
        <div class="bg-white rounded-[22px] p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 rounded-[10px] bg-[#C8EBE5] text-[#0B6E6C] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[#111827] truncate">Template Catatan Harian Cepat</p>
                    <p class="text-[10px] text-zinc-500 truncate">Isi otomatis kegiatan rutin harian dengan 1 klik</p>
                </div>
            </div>
            <button type="button"
                    onclick="openAdvancedFeatureModal('template')"
                    class="shrink-0 bg-[#FDE9B4] text-[#78350F] text-[10px] font-bold px-3 py-1.5 rounded-full flex items-center gap-1">
                <i class="fa-solid fa-lock text-[9px]"></i>
                <span>Fitur Lanjutan</span>
            </button>
        </div>

        <form id="logbook-form" action="{{ route('logbook.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="source" id="input-source" value="manual">
            <input type="hidden" name="category" value="tugas">
            <input type="hidden" name="ai_description" id="input-ai-description" value="">
            <input type="hidden" name="final_description" id="input-final-description" value="">
            <input type="hidden" name="status" value="approved">

            <!-- Bento 2-Column Date & Time -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-[24px] p-4 shadow-2xs flex flex-col justify-between">
                    <label for="input-date" class="block text-[11px] font-semibold text-zinc-400">Tanggal Kegiatan</label>
                    <div class="mt-2 flex items-center justify-between gap-1.5 cursor-pointer">
                        <input type="date" name="date" id="input-date" value="{{ old('date', $targetDate) }}" required
                               class="w-full border-0 p-0 text-sm font-bold text-[#111827] focus:ring-0 bg-transparent cursor-pointer tracking-tight">
                        <i class="fa-regular fa-calendar text-zinc-400 text-xs pointer-events-none shrink-0"></i>
                    </div>
                </div>

                <div class="bg-white rounded-[24px] p-4 shadow-2xs flex flex-col justify-between">
                    <label class="block text-[11px] font-semibold text-zinc-400">Jam Mulai & Selesai</label>
                    <div class="mt-2 flex items-center justify-between gap-1 text-[#111827]">
                        <div class="flex items-center gap-1 min-w-0 flex-1">
                            <input type="time" name="start_time" id="input-start" value="{{ old('start_time', '08:30') }}" required
                                   class="w-full border-0 p-0 text-xs font-bold text-[#111827] focus:ring-0 bg-transparent text-center cursor-pointer">
                            <i class="fa-regular fa-clock text-zinc-400 text-[10px] pointer-events-none shrink-0"></i>
                        </div>
                        <span class="text-zinc-300 font-bold px-0.5 text-xs">–</span>
                        <div class="flex items-center gap-1 min-w-0 flex-1">
                            <input type="time" name="end_time" id="input-end" value="{{ old('end_time', '12:00') }}" required
                                   class="w-full border-0 p-0 text-xs font-bold text-[#111827] focus:ring-0 bg-transparent text-center cursor-pointer">
                            <i class="fa-regular fa-clock text-zinc-400 text-[10px] pointer-events-none shrink-0"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Title & Lokasi Kegiatan (Kantor / Lapangan Only, Kategori Hidden) -->
            <div class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Judul Kegiatan Harian</label>
                    <input type="text" name="title" id="input-title" value="{{ old('title') }}" required
                           placeholder="Contoh: Pengerjaan Sistem HR & Rekap Data Harian"
                           class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-sm text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Lokasi Kegiatan</label>
                    <select name="work_location"
                            class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
                        <option value="kantor">Kantor</option>
                        <option value="lapangan">Lapangan</option>
                    </select>
                </div>
            </div>

            <!-- Description Card + AI & Voice Advanced Feature Triggers -->
            <section class="bg-kinra-grid rounded-[28px] p-5 space-y-3 shadow-xs text-white">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-pen-clip"></i>
                        <span>Deskripsi Kegiatan Harian</span>
                    </span>
                    <button type="button"
                            onclick="openAdvancedFeatureModal('ai')"
                            class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-[#0B6E6C] shadow-2xs hover:bg-[#FDE9B4] transition flex items-center gap-1">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>AI & Voice: Fitur Lanjutan</span>
                    </button>
                </div>

                <textarea name="raw_description" id="input-raw" rows="3" required
                          placeholder="Tuliskan rincian kegiatan harian yang kamu kerjakan hari ini..."
                          class="w-full rounded-[18px] bg-white border-0 p-3.5 text-xs text-[#111827] leading-relaxed focus:ring-2 focus:ring-[#FDE9B4]">{{ old('raw_description') }}</textarea>

                <!-- AI & Voice Action Buttons (Font Awesome Icons) -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button"
                            onclick="openAdvancedFeatureModal('ai')"
                            class="bg-[#18181B] text-white text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-xs hover:opacity-90 transition">
                        <i class="fa-solid fa-wand-magic-sparkles text-[#FDE9B4]"></i>
                        <span>Rapikan AI</span>
                        <span class="bg-[#C8EBE5] text-[#074745] text-[9px] font-bold px-1.5 py-0.5 rounded-full">Segera</span>
                    </button>

                    <button type="button"
                            onclick="openAdvancedFeatureModal('voice')"
                            class="bg-white text-[#074745] text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-2xs hover:bg-[#C8EBE5] transition">
                        <i class="fa-solid fa-microphone-lines text-[#0B6E6C]"></i>
                        <span>Rekam Suara</span>
                        <span class="bg-[#FDE9B4] text-[#78350F] text-[9px] font-bold px-1.5 py-0.5 rounded-full">Lanjutan</span>
                    </button>

                    <button type="button"
                            onclick="openAdvancedFeatureModal('photo')"
                            class="bg-white text-[#074745] text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-2xs hover:bg-[#D4EEFC] transition">
                        <i class="fa-solid fa-camera text-[#0B6E6C]"></i>
                        <span>Ambil Foto</span>
                    </button>
                </div>
            </section>

            <!-- Output, Obstacles & Photo Upload -->
            <div class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Hasil / Output Kerja (Pisahkan dengan •)</label>
                    <input type="text" name="output" id="input-output" value="{{ old('output') }}"
                           placeholder="Contoh: Modul HR System • Rekap Laporan Harian"
                           class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Kendala (Opsional)</label>
                    <input type="text" name="obstacles" value="{{ old('obstacles') }}"
                           placeholder="Contoh: Penyesuaian format data laporan"
                           class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Lampiran Bukti Foto (Opsional)</label>
                    <input type="file" name="photo" accept="image/*"
                           class="mt-1.5 block w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#C8EBE5] file:text-[#074745]">
                </div>
            </div>

            <!-- Single Direct Submit Button -->
            <button type="submit"
                    class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-sm font-bold py-4 rounded-full shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Logbook Harian</span>
            </button>
        </form>
    @endif
</div>

<script>
    function applyTemplate(title, description) {
        const titleEl = document.getElementById('input-title');
        const rawEl = document.getElementById('input-raw');
        if (titleEl) titleEl.value = title;
        if (rawEl) rawEl.value = description;
    }
</script>
@endsection
