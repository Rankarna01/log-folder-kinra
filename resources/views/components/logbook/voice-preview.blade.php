@props(['target'])

<!-- Pratinjau hasil rekam suara: teks ditampilkan di sini dulu sebelum dimasukkan ke deskripsi -->
<div id="voice-preview-{{ $target }}" class="hidden rounded-[18px] bg-white p-3.5 space-y-2.5 text-xs text-[#111827]">
    <div class="flex items-center justify-between gap-2">
        <span class="font-semibold text-[#074745]">Pratinjau Rekam Suara</span>
        <span data-voice-preview-state class="text-[11px] font-medium text-zinc-500"></span>
    </div>
    <p data-voice-preview-text class="leading-relaxed whitespace-pre-line text-[#111827]"></p>
    <p data-voice-preview-error class="hidden text-[11px] font-medium text-[#B91C1C]"></p>
    <div data-voice-preview-actions class="hidden justify-end gap-2 pt-1">
        <button type="button" data-voice-discard
                class="h-8 rounded-full border border-zinc-300 bg-white px-3.5 text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition">
            Buang
        </button>
        <button type="button" data-voice-apply
                class="h-8 rounded-full bg-[#0B6E6C] px-3.5 text-[11px] font-semibold text-white hover:bg-[#085856] transition">
            Masukkan ke deskripsi
        </button>
    </div>
</div>
