@extends('layouts.app')

@section('content')
<div class="px-5 pt-4 pb-8 space-y-4">

    <header class="flex items-center justify-between">
        <a href="{{ route('logbook.show', $logbook) }}"
           class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#18181B] shadow-2xs">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <h1 class="text-[17px] font-semibold text-[#111827]">Edit Entri Logbook</h1>
        <form action="{{ route('logbook.destroy', $logbook) }}" method="POST" onsubmit="return confirm('Hapus logbook ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-[#F8C8C4] text-[#18181B] text-xs font-bold px-3 py-1.5 rounded-full">
                Hapus
            </button>
        </form>
    </header>

    <form action="{{ route('logbook.update', $logbook) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="approved">

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <label class="block text-[11px] font-semibold text-zinc-400">Tanggal</label>
                <input type="date" name="date" value="{{ $logbook->date->format('Y-m-d') }}" required
                       class="mt-1 w-full border-0 p-0 text-sm font-semibold text-[#111827] bg-transparent">
            </div>

            <div class="bg-white rounded-[24px] p-4 shadow-2xs">
                <label class="block text-[11px] font-semibold text-zinc-400">Jam Kerja</label>
                <div class="mt-1 flex items-center gap-1">
                    <input type="time" name="start_time" value="{{ substr($logbook->start_time, 0, 5) }}" required
                           class="w-full border-0 p-0 text-xs font-semibold text-[#111827] bg-transparent">
                    <span>-</span>
                    <input type="time" name="end_time" value="{{ substr($logbook->end_time, 0, 5) }}" required
                           class="w-full border-0 p-0 text-xs font-semibold text-[#111827] bg-transparent">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[26px] p-4 space-y-3 shadow-2xs">
            <div>
                <label class="block text-xs font-semibold text-[#111827]">Judul Kegiatan</label>
                <input type="text" name="title" value="{{ $logbook->title }}" required
                       class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-sm text-[#111827]">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Kategori</label>
                    <select name="category" class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3 py-2.5 text-xs font-semibold">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->value }}" @selected($logbook->category === $cat)>{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#111827]">Lokasi</label>
                    <select name="work_location" class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3 py-2.5 text-xs font-semibold">
                        <option value="wfo" @selected($logbook->work_location === 'wfo')>WFO</option>
                        <option value="wfh" @selected($logbook->work_location === 'wfh')>WFH</option>
                        <option value="field" @selected($logbook->work_location === 'field')>Lapangan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Deskripsi Kegiatan</label>
                <textarea name="final_description" rows="4" required
                          class="mt-1.5 w-full rounded-[16px] bg-[#F6F7F9] border-0 p-3 text-xs text-[#111827] leading-relaxed">{{ $logbook->final_description }}</textarea>
                <input type="hidden" name="raw_description" value="{{ $logbook->raw_description }}">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Hasil / Output</label>
                <input type="text" name="output" value="{{ $logbook->output }}"
                       class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111827]">Kendala</label>
                <input type="text" name="obstacles" value="{{ $logbook->obstacles }}"
                       class="mt-1.5 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs text-[#111827]">
            </div>
        </div>

        <button type="submit"
                class="w-full bg-[#18181B] text-white text-sm font-bold py-3.5 rounded-full shadow-md">
            Simpan Perubahan Logbook →
        </button>
    </form>
</div>
@endsection
