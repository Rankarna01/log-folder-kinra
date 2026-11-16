@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-8 space-y-4">

    <header class="bg-kinra-grid rounded-[28px] p-5 flex items-center justify-between shadow-2xs text-white">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-14 h-14 rounded-full bg-white text-[#0B6E6C] border-2 border-emerald-200 flex items-center justify-center text-lg font-bold shrink-0 shadow-xs">
                {{ $user->initials }}
            </div>
            <div class="min-w-0">
                <span class="bg-white/95 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-[#0B6E6C]">
                    Peserta Magang KINRA
                </span>
                <h1 class="text-lg font-bold text-white mt-1 truncate">{{ $user->name }}</h1>
                <p class="text-xs text-emerald-100 truncate">{{ $internship?->division }} • {{ $internship?->position }}</p>
            </div>
        </div>
        <a href="{{ route('profile.export.csv') }}"
           class="bg-white text-[#0B6E6C] text-xs font-bold px-3.5 py-2 rounded-full shrink-0 shadow-2xs">
            Ekspor CSV
        </a>
    </header>

    <!-- Placement Info Bento (Division, Position, Mentor) -->
    @if ($internship)
        <section class="grid grid-cols-2 gap-3">
            <div class="bg-[#C8EBE5] rounded-[24px] p-4">
                <p class="text-[11px] text-[#074745] font-medium">Divisi & Bagian</p>
                <p class="text-xs font-bold text-[#111827] mt-1">{{ $internship->division }}</p>
                <p class="text-[11px] text-zinc-700 mt-0.5">{{ $internship->position }}</p>
            </div>
            <div class="bg-[#D4EEFC] rounded-[24px] p-4">
                <p class="text-[11px] text-[#0C4A6E] font-medium">Mentor Pembimbing</p>
                <p class="text-xs font-bold text-[#111827] mt-1">{{ $internship->mentor?->name ?? 'Agnes Nielsen' }}</p>
                <p class="text-[11px] text-zinc-700 mt-0.5">Pembimbing Lapangan</p>
            </div>
        </section>
    @endif

    <!-- Update 4 Core Registration Fields (Nama, Divisi, Bagian, Mentor) -->
    <form action="{{ route('profile.update') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3.5 shadow-2xs">
        @csrf
        @method('PUT')
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-[#111827]">Data Registrasi Peserta</h2>
            <a href="{{ route('register') }}" class="text-xs font-semibold text-[#0B6E6C] hover:underline">
                + Daftar Peserta Baru
            </a>
        </div>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Nama Lengkap Peserta</label>
            <input type="text" name="name" value="{{ $user->name }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Divisi</label>
            <input type="text" name="division" value="{{ $internship?->division ?? $user->campus }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Bagian / Posisi</label>
            <input type="text" name="position" value="{{ $internship?->position ?? $user->major }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Nama Mentor</label>
                <input type="text" name="mentor_name" value="{{ $internship?->mentor?->name ?? ($user->preferences['mentor_name'] ?? 'Agnes Nielsen') }}" required
                       class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Gaya AI Default</label>
                <select name="ai_style" class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3 py-2.5 text-xs font-semibold text-[#111827]">
                    <option value="formal" @selected(($user->preferences['ai_style'] ?? 'formal') === 'formal')>Formal Baku</option>
                    <option value="singkat" @selected(($user->preferences['ai_style'] ?? '') === 'singkat')>Singkat Padat</option>
                    <option value="detail" @selected(($user->preferences['ai_style'] ?? '') === 'detail')>Detail Terstruktur</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-xs font-bold py-3 rounded-full">
            Simpan Data Peserta
        </button>
    </form>

    <!-- Add Activity Template -->
    <form action="{{ route('profile.templates.store') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3 shadow-2xs">
        @csrf
        <h2 class="text-sm font-semibold text-[#111827]">Tambah Template Kegiatan Berulang</h2>
        <input type="text" name="title" required placeholder="Judul template (misal: Daily Standup)"
               class="w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2 text-xs">
        <input type="hidden" name="category" value="tugas">
        <textarea name="description" rows="2" required placeholder="Deskripsi default..."
                  class="w-full rounded-[14px] bg-[#F4F7F6] border-0 p-3 text-xs"></textarea>
        <button type="submit" class="bg-[#C8EBE5] text-[#074745] text-xs font-bold px-4 py-2 rounded-full">
            + Simpan Template
        </button>
    </form>

</div>
@endsection
