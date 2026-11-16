@extends('layouts.app')

@section('content')
<div class="px-5 pt-4 pb-8 space-y-4">

    <header class="flex items-center justify-between">
        <a href="{{ route('logbook.index') }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-2xs">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <h1 class="text-[17px] font-semibold text-[#111827]">Tambah Logbook Harian</h1>
        <span class="bg-[#E5D9FA] text-[#18181B] text-[11px] font-bold px-3 py-1 rounded-full">
            AI {{ $quota['remaining'] }}/{{ $quota['limit'] }}
        </span>
    </header>

    <!-- Activity Templates Quick Fill -->
    @if ($templates->isNotEmpty())
        <div class="space-y-1.5">
            <p class="text-[11px] font-semibold text-zinc-500">Template Kegiatan Cepat (1-Tap):</p>
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                @foreach ($templates as $tpl)
                    <button type="button"
                            onclick="applyTemplate(@js($tpl->title), @js($tpl->category), @js($tpl->description))"
                            class="shrink-0 bg-white hover:bg-[#D5EDE8] text-[#111827] text-xs font-medium px-3 py-1.5 rounded-full shadow-2xs transition">
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
                <label class="block text-xs font-semibold text-[#111827]">Judul Kegiatan</label>
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

        <!-- AI Gemini Assistant & Raw Description Card (Lavender Grid Style) -->
        <section class="bg-lavender-grid rounded-[28px] p-5 space-y-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#111827] flex items-center gap-1.5">
                    <span>✨</span> Catatan Kegiatan & Asisten AI Gemini
                </span>
                <!-- Style Selector Pill -->
                <select id="ai-style-select" class="rounded-full bg-white border-0 px-3 py-1 text-[11px] font-semibold text-[#18181B]">
                    <option value="formal">Gaya: Formal</option>
                    <option value="singkat">Gaya: Singkat</option>
                    <option value="detail">Gaya: Detail</option>
                </select>
            </div>

            <textarea name="raw_description" id="input-raw" rows="3" required
                      placeholder="Tulis catatan singkat atau gunakan mikrofon (misal: bikin komponen uiux dashboard dan meeting sama mentor agnes)..."
                      class="w-full rounded-[18px] bg-white/95 border-0 p-3.5 text-xs text-[#111827] leading-relaxed focus:ring-2 focus:ring-[#18181B]">{{ old('raw_description') }}</textarea>

            <!-- AI Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="polishWithGemini()"
                        id="btn-polish"
                        class="bg-[#18181B] text-white text-xs font-semibold px-4 py-2 rounded-full flex items-center gap-1.5 shadow-xs hover:opacity-95 transition">
                    <span>✨</span> Rapikan Teks
                </button>

                <button type="button" onclick="startVoiceDictation()"
                        id="btn-voice"
                        class="bg-white text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-2xs hover:bg-[#D5EDE8] transition">
                    <span>🎤</span> Rekam Suara
                </button>

                <button type="button" onclick="suggestFromPhoto()"
                        class="bg-white text-[#18181B] text-xs font-semibold px-3.5 py-2 rounded-full flex items-center gap-1.5 shadow-2xs hover:bg-[#FBE8A6] transition">
                    <span>📷</span> Saran Foto
                </button>
            </div>

            <!-- AI Comparison Preview Panel -->
            <div id="ai-result-panel" class="hidden bg-[#18181B] text-white rounded-[20px] p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-[#E5D9FA]">✨ Hasil Perapian AI Gemini:</span>
                    <span id="ai-quality-badge" class="text-[10px] bg-white/15 px-2.5 py-0.5 rounded-full">Skor: 95/100</span>
                </div>
                <p id="ai-result-text" class="text-xs text-zinc-100 leading-relaxed"></p>
                <div class="flex items-center gap-2 pt-1">
                    <button type="button" onclick="applyAiResult()"
                            class="bg-[#D5EDE8] text-[#18181B] text-xs font-bold px-3.5 py-1.5 rounded-full">
                        ✓ Terapkan Hasil AI
                    </button>
                    <button type="button" onclick="document.getElementById('ai-result-panel').classList.add('hidden')"
                            class="bg-white/10 text-zinc-300 text-xs font-medium px-3 py-1.5 rounded-full">
                        Abaikan
                    </button>
                </div>
            </div>
        </section>

        <!-- Output, Obstacles & Photo Watermark Upload -->
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
                <label class="block text-xs font-semibold text-[#111827]">Lampiran Bukti Foto (Otomatis Kompres & Watermark)</label>
                <input type="file" name="photo" accept="image/*"
                       class="mt-1.5 block w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#E5D9FA] file:text-[#18181B]">
            </div>
        </div>

        <!-- Submit Buttons (Draft vs Submit to Mentor) -->
        <div class="grid grid-cols-2 gap-3 pt-1">
            <button type="submit" name="status" value="draft"
                    class="bg-white border-2 border-[#18181B] text-[#18181B] text-xs font-bold py-3.5 rounded-full shadow-xs">
                Simpan Draft
            </button>
            <button type="submit" name="status" value="submitted"
                    class="bg-[#18181B] text-white text-xs font-bold py-3.5 rounded-full shadow-md">
                Simpan & Submit →
            </button>
        </div>
    </form>
</div>

<script>
    function applyTemplate(title, category, description) {
        document.getElementById('input-title').value = title;
        document.getElementById('input-category').value = category;
        document.getElementById('input-raw').value = description;
    }

    async function polishWithGemini() {
        const raw = document.getElementById('input-raw').value.trim();
        if (!raw) {
            alert('Tulis catatan kasar terlebih dahulu sebelum dirapikan AI.');
            return;
        }
        const style = document.getElementById('ai-style-select').value;
        const output = document.getElementById('input-output').value;
        const btn = document.getElementById('btn-polish');
        btn.textContent = '⏳ Merapikan...';

        try {
            const res = await fetch('{{ route("api.ai.polish") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ text: raw, style, output }),
            });
            const data = await res.json();
            if (data.result) {
                document.getElementById('ai-result-text').textContent = data.result;
                document.getElementById('input-ai-description').value = data.result;
                if (data.quality) {
                    document.getElementById('ai-quality-badge').textContent = `Skor: ${data.quality.score}/100 (${data.quality.status})`;
                }
                document.getElementById('ai-result-panel').classList.remove('hidden');
            }
        } catch (e) {
            alert('Sedang offline. Catatan asli tetap akan disimpan.');
        } finally {
            btn.innerHTML = '<span>✨</span> Rapikan Teks';
        }
    }

    function applyAiResult() {
        const aiText = document.getElementById('ai-result-text').textContent;
        document.getElementById('input-final-description').value = aiText;
        document.getElementById('input-raw').value = aiText;
        document.getElementById('ai-result-panel').classList.add('hidden');
    }

    async function startVoiceDictation() {
        const btn = document.getElementById('btn-voice');
        btn.textContent = '🎙️ Mendengarkan...';
        document.getElementById('input-source').value = 'voice';

        try {
            const res = await fetch('{{ route("api.ai.transcribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    voice_text: document.getElementById('input-raw').value || 'hari ini saya bikin desain komponen uiux dashboard dan meeting sama mentor agnes buat review wireframe aplikasi',
                    style: document.getElementById('ai-style-select').value
                }),
            });
            const data = await res.json();
            document.getElementById('input-raw').value = data.transcript;
            document.getElementById('ai-result-text').textContent = data.polished;
            document.getElementById('input-ai-description').value = data.polished;
            document.getElementById('ai-result-panel').classList.remove('hidden');
        } catch (e) {} finally {
            btn.innerHTML = '<span>🎤</span> Rekam Suara';
        }
    }

    async function suggestFromPhoto() {
        const res = await fetch('{{ route("api.ai.photo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ filename: 'dokumentasi-magang.jpg' }),
        });
        const data = await res.json();
        if (!document.getElementById('input-raw').value) {
            document.getElementById('input-raw').value = data.suggestion;
        }
        if (!document.getElementById('input-output').value) {
            document.getElementById('input-output').value = data.caption;
        }
    }
</script>
@endsection
