@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-8 space-y-4">

    <header class="bg-lavender-grid rounded-[28px] p-5 flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-14 h-14 rounded-full bg-[#F8C8C4] border-2 border-white flex items-center justify-center text-lg font-bold text-[#18181B] shrink-0">
                {{ $user->initials }}
            </div>
            <div class="min-w-0">
                <span class="bg-white px-2.5 py-0.5 rounded-full text-[10px] font-bold text-[#18181B]">
                    Peserta Magang
                </span>
                <h1 class="text-lg font-bold text-[#111827] mt-1 truncate">{{ $user->name }}</h1>
                <p class="text-xs text-zinc-600 truncate">{{ $internship?->division }} • {{ $internship?->position }}</p>
            </div>
        </div>
        <a href="{{ route('profile.export.csv') }}"
           class="bg-[#18181B] text-white text-xs font-semibold px-3.5 py-2 rounded-full shrink-0">
            Ekspor CSV
        </a>
    </header>

    <!-- Placement Info Bento (Division, Position, Mentor) -->
    @if ($internship)
        <section class="grid grid-cols-2 gap-3">
            <div class="bg-[#D5EDE8] rounded-[24px] p-4">
                <p class="text-[11px] text-zinc-600 font-medium">Divisi & Bagian</p>
                <p class="text-xs font-bold text-[#111827] mt-1">{{ $internship->division }}</p>
                <p class="text-[11px] text-zinc-600 mt-0.5">{{ $internship->position }}</p>
            </div>
            <div class="bg-[#E6E8FA] rounded-[24px] p-4">
                <p class="text-[11px] text-zinc-600 font-medium">Mentor Pembimbing</p>
                <p class="text-xs font-bold text-[#111827] mt-1">{{ $internship->mentor?->name ?? 'Agnes Nielsen' }}</p>
                <p class="text-[11px] text-zinc-600 mt-0.5">Pembimbing Lapangan</p>
            </div>
        </section>
    @endif

    <!-- Update 4 Core Registration Fields (Nama, Divisi, Bagian, Mentor) -->
    <form action="{{ route('profile.update') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3.5 shadow-2xs">
        @csrf
        @method('PUT')
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-[#111827]">Data Registrasi Peserta</h2>
            <a href="{{ route('register') }}" class="text-xs font-semibold text-purple-800 hover:underline">
                + Daftar Peserta Baru
            </a>
        </div>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Nama Lengkap Peserta</label>
            <input type="text" name="name" value="{{ $user->name }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Divisi</label>
            <input type="text" name="division" value="{{ $internship?->division ?? $user->campus }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Bagian / Posisi</label>
            <input type="text" name="position" value="{{ $internship?->position ?? $user->major }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Nama Mentor</label>
                <input type="text" name="mentor_name" value="{{ $internship?->mentor?->name ?? ($user->preferences['mentor_name'] ?? 'Agnes Nielsen') }}" required
                       class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Gaya AI Default</label>
                <select name="ai_style" class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3 py-2.5 text-xs font-semibold text-[#111827]">
                    <option value="formal" @selected(($user->preferences['ai_style'] ?? 'formal') === 'formal')>Formal Baku</option>
                    <option value="singkat" @selected(($user->preferences['ai_style'] ?? '') === 'singkat')>Singkat Padat</option>
                    <option value="detail" @selected(($user->preferences['ai_style'] ?? '') === 'detail')>Detail Terstruktur</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full bg-[#18181B] text-white text-xs font-bold py-3 rounded-full">
            Simpan Data Peserta
        </button>
    </form>

    <!-- Add Activity Template -->
    <form action="{{ route('profile.templates.store') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3 shadow-2xs">
        @csrf
        <h2 class="text-sm font-semibold text-[#111827]">Tambah Template Kegiatan Berulang</h2>
        <input type="text" name="title" required placeholder="Judul template (misal: Daily Standup)"
               class="w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2 text-xs">
        <input type="hidden" name="category" value="tugas">
        <textarea name="description" rows="2" required placeholder="Deskripsi default..."
                  class="w-full rounded-[14px] bg-[#F6F7F9] border-0 p-3 text-xs"></textarea>
        <button type="submit" class="bg-[#E5D9FA] text-[#18181B] text-xs font-bold px-4 py-2 rounded-full">
            + Simpan Template
        </button>
    </form>

</div>
@endsection
