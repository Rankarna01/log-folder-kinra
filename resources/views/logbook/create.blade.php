@extends('layouts.app')

@section('content')
<div class="px-5 pt-4 pb-8 space-y-4">

    <header class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-2xs">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <h1 class="text-[17px] font-semibold text-[#111827]">Tambah Logbook Harian</h1>
        <button type="button"
                onclick="openAdvancedFeatureModal('ai')"
                class="bg-[#E5D9FA] hover:bg-[#D5C2F7] text-[#18181B] text-[11px] font-bold px-3 py-1 rounded-full transition">
            ✨ Fitur AI
        </button>
    </header>

    <!-- Active Participant Info Banner (Shows Registered Name, Division, Position, Mentor) -->
    @if ($internship)
        <div class="bg-[#D5EDE8] rounded-[22px] px-4 py-3 flex items-center justify-between text-xs">
            <div class="min-w-0">
                <p class="font-bold text-[#111827] truncate">
                    {{ auth()->user()?->name ?? $internship->participant?->name }} • {{ $internship->division }}
                </p>
                <p class="text-[11px] text-zinc-600 truncate">
                    Bagian: {{ $internship->position ?? '-' }} • Mentor: {{ $internship->mentor?->name ?? '-' }}
                </p>
            </div>
            <a href="{{ route('profile.show') }}" class="bg-white text-[#18181B] text-[11px] font-semibold px-3 py-1 rounded-full shrink-0 ml-2">
                Ubah Data
            </a>
        </div>
    @endif

    <!-- Activity Templates Quick Fill -->
    @if ($templates->isNotEmpty())
        <div class="space-y-1.5">
            <p class="text-[11px] font-semibold text-zinc-500">Template Kegiatan Cepat (1-Tap):</p>
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                @foreach ($templates as $tpl)
                    <button type="button"
                            onclick="applyTemplate(@js($tpl->title), @js($tpl->category), @js($tpl->description))"
                            class="shrink-0 bg-white hover:bg-[#D5EDE8] text-[#11181B] text-xs font-medium px-3 py-1.5 rounded-full shadow-2xs transition">
                        ⚡ {{ $tpl->title }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <form id="logbook-form" action="{{ route('logbook.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="hidden" name="source" id="input-source" value="manual">
        <input type="hidden" name="ai_description" id="input-ai-description" value="">
        <input type="hidden" name="final_description" id="input-final-description" value="">
        <input type="hidden" name="status" value="approved">

        <!-- Bento 2-Column Date & Time -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <label class="block text-[11px] font-semibold text-zinc-400">Tanggal Kegiatan</label>
                <input type="date" name="date" id="input-date" value="{{ old('date', '2026-11-16') }}" required
                       class="mt-1 w-full border-0 p-0 text-sm font-semibold text-[#111827] focus:ring-0 bg-transparent">
            </div>

            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <label class="block text-[11px] font-semibold text-zinc-400">Jam Mulai & Selesai</label>
                <div class="mt-1 flex items-center gap-1">
                    <input type="time" name="start_time" id="input-start" value="{{ old('start_time', '08:30') }}" required
                           class="w-full border-0 p-0 text-xs font-semibold text-[#111827] focus:ring-0 bg-transparent">
                    <span class="text-zinc-400">-</span>
                    <input type="time" name="end_time" id="input-end" value="{{ old('end_time', '12:00') }}" required
                           class="w-full border-0 p-0 text-xs font-semibold text-[#111827] focus:ring-0 bg-transparent">
                </div>
            </div>
        </div>

        <!-- Title & Category Card -->
        <div class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
            <div>
                <label class="block text-xs font-semibold text-[#111827]">Judul Kegiatan Harian</label>
                <input type="text" name="title" id="input-title" value="{{ old('title') }}" required
                       placeholder="Contoh: Penyusunan Design System & UI Kit"
                       class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-sm text-[#111827] focus:ring-2 focus:ring-[#18181B]">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Kategori</label>
                    <select name="category" id="input-category"
                            class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3 py-2.5 text-xs font-semibold text-[#111827]">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Lokasi Kerja</label>
                    <select name="work_location"
                            class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3 py-2.5 text-xs font-semibold text-[#111827]">
                        <option value="wfo">WFO (Kantor)</option>
                        <option value="wfh">WFH (Rumah)</option>
                        <option value="field">Lapangan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Description Card + AI & Voice Advanced Feature Triggers (Lavender Grid Style) -->
        <section class="bg-lavender-grid rounded-[28px] p-5 space-y-3 shadow-xs">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-bold text-[#111827] flex items-center gap-1.5">
                    <span>📝</span> Deskripsi Kegiatan Harian
                </span>
                <button type="button"
                        onclick="openAdvancedFeatureModal('ai')"
                        class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-[#18181B] shadow-2xs hover:bg-[#FBE8A6] transition">
                    ✨ AI & Voice: Fitur Lanjutan
                </button>
            </div>

            <textarea name="raw_description" id="input-raw" rows="3" required
                      placeholder="Tuliskan rincian kegiatan harian yang kamu kerjakan hari ini..."
                      class="w-full rounded-[18px] bg-white/95 border-0 p-3.5 text-xs text-[#111827] leading-relaxed focus:ring-2 focus:ring-[#18181B]">{{ old('raw_description') }}</textarea>

            <!-- AI & Voice Action Buttons -> Opens Stylized Advanced Feature Modal Alert -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button"
                        onclick="openAdvancedFeatureModal('ai')"
                        class="bg-[#18181B] text-white text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-xs hover:opacity-90 transition">
                    <span>✨</span> Rapikan AI
                    <span class="bg-[#E5D9FA] text-[#18181B] text-[9px] font-bold px-1.5 py-0.5 rounded-full">Segera</span>
                </button>

                <button type="button"
                        onclick="openAdvancedFeatureModal('voice')"
                        class="bg-white text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-2xs hover:bg-[#D5EDE8] transition">
                    <span>🎤</span> Rekam Suara
                    <span class="bg-[#FBE8A6] text-[#18181B] text-[9px] font-bold px-1.5 py-0.5 rounded-full">Lanjutan</span>
                </button>

                <button type="button"
                        onclick="openAdvancedFeatureModal('photo')"
                        class="bg-white text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-2xs hover:bg-[#FBE8A6] transition">
                    <span>📷</span> Saran Foto
                </button>
            </div>
        </section>

        <!-- Output, Obstacles & Photo Upload -->
        <div class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
            <div>
                <label class="block text-xs font-semibold text-[#111827]">Hasil / Output Kerja (Pisahkan dengan •)</label>
                <input type="text" name="output" id="input-output" value="{{ old('output') }}"
                       placeholder="Contoh: Design system • Landing Page • Pricing Page"
                       class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Kendala (Opsional)</label>
                <input type="text" name="obstacles" value="{{ old('obstacles') }}"
                       placeholder="Contoh: Penyesuaian kontras warna pada layar mobile"
                       class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Lampiran Bukti Foto (Opsional)</label>
                <input type="file" name="photo" accept="image/*"
                       class="mt-1.5 block w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#E5D9FA] file:text-[#18181B]">
            </div>
        </div>

        <!-- Single Direct Submit Button -->
        <button type="submit"
                class="w-full bg-[#18181B] hover:bg-zinc-800 text-white text-sm font-bold py-4 rounded-full shadow-md transition active:scale-[0.99]">
            Simpan Logbook Harian →
        </button>
    </form>
</div>

<script>
    function applyTemplate(title, category, description) {
        document.getElementById('input-title').value = title;
        document.getElementById('input-category').value = category;
        document.getElementById('input-raw').value = description;
    }
</script>
@endsection
