@extends('layouts.app')

@section('content')
<div class="px-5 pt-4 pb-8 space-y-4">

    <header class="flex items-center justify-between">
        <a href="{{ route('logbook.show', $logbook) }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-2xs">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </a>
        <h1 class="text-[17px] font-semibold text-[#111827]">Edit Entri Logbook</h1>
        <form action="{{ route('logbook.destroy', $logbook) }}" method="POST" onsubmit="return confirm('Hapus logbook ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-[#FCE1D4] text-[#9A3412] text-xs font-bold px-3.5 py-1.5 rounded-full flex items-center gap-1.5">
                <i class="fa-solid fa-trash-can text-[11px]"></i>
                <span>Hapus</span>
            </button>
        </form>
    </header>

    <form action="{{ route('logbook.update', $logbook) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="approved">
        <input type="hidden" name="category" value="tugas">

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <label for="input-date" class="block text-[11px] font-semibold text-zinc-400">Tanggal Kegiatan</label>
                <div class="mt-1 relative flex items-center justify-between">
                    <input type="date" name="date" id="input-date" value="{{ $logbook->date->format('Y-m-d') }}" required
                           class="w-full border-0 p-0 text-sm font-bold text-[#111827] focus:ring-0 bg-transparent cursor-pointer tracking-tight">
                    <i class="fa-regular fa-calendar text-zinc-400 text-xs shrink-0 pointer-events-none"></i>
                </div>
            </div>

            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <label class="block text-[11px] font-semibold text-zinc-400">Jam Mulai & Selesai</label>
                <div class="mt-1 flex items-center justify-between gap-1 text-[#111827]">
                    <input type="time" name="start_time" value="{{ substr($logbook->start_time, 0, 5) }}" required
                           class="w-full border-0 p-0 text-sm font-bold text-[#111827] focus:ring-0 bg-transparent cursor-pointer">
                    <span class="text-zinc-300 font-bold px-0.5 text-xs">–</span>
                    <input type="time" name="end_time" value="{{ substr($logbook->end_time, 0, 5) }}" required
                           class="w-full border-0 p-0 text-sm font-bold text-[#111827] focus:ring-0 bg-transparent cursor-pointer">
                    <i class="fa-regular fa-clock text-zinc-400 text-xs shrink-0 pointer-events-none ml-1"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
            <div>
                <label class="block text-xs font-semibold text-[#111827]">Judul Kegiatan Harian</label>
                <input type="text" name="title" value="{{ $logbook->title }}" required
                       class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-sm text-[#111827]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Lokasi Kegiatan</label>
                @php $isLapangan = $logbook->location_label === 'Lapangan'; @endphp
                <select name="work_location" class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
                    <option value="kantor" @selected(! $isLapangan)>Kantor</option>
                    <option value="lapangan" @selected($isLapangan)>Lapangan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Deskripsi Kegiatan</label>
                <textarea name="final_description" rows="4" required
                          class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 p-3 text-xs text-[#111827] leading-relaxed">{{ $logbook->final_description }}</textarea>
                <input type="hidden" name="raw_description" value="{{ $logbook->raw_description }}">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Hasil / Output (Teks atau Link Dokumen / Drive)</label>
                <input type="text" name="output" value="{{ $logbook->output }}"
                       placeholder="Contoh: https://docs.google.com/... atau Laporan Akhir"
                       class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Kendala</label>
                <input type="text" name="obstacles" value="{{ $logbook->obstacles }}"
                       class="mt-1.5 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
            </div>
        </div>

        <button type="submit"
                class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-sm font-bold py-3.5 rounded-full shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Simpan Perubahan Logbook</span>
        </button>
    </form>
</div>
@endsection
