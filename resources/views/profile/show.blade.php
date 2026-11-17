@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-8 space-y-4">

    <header class="bg-kinra-grid rounded-[28px] p-5 flex items-center justify-between shadow-2xs text-white">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-14 h-14 rounded-full bg-white text-[#0B6E6C] border-2 border-emerald-200 flex items-center justify-center text-xl font-bold shrink-0 shadow-xs">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-lg font-bold text-white mt-1 truncate">{{ $user->name }}</h1>
                <p class="text-xs text-emerald-100 truncate">{{ $internship?->division }} • {{ $internship?->position }}</p>
            </div>
        </div>
        <a href="{{ route('profile.export.csv') }}"
           class="bg-white text-[#0B6E6C] text-xs font-bold px-3.5 py-2 rounded-full shrink-0 shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-file-csv"></i>
            <span>Ekspor CSV</span>
        </a>
    </header>

    <!-- Placement Info Bento (Division, Position, Mentor) -->
    @if ($internship)
        <section class="grid grid-cols-2 gap-3">
            <div class="bg-[#C8EBE5] rounded-[24px] p-4">
                <p class="text-[11px] text-[#074745] font-medium flex items-center gap-1">
                    <i class="fa-solid fa-building text-[10px]"></i>
                    <span>Divisi & Bagian</span>
                </p>
                <p class="text-xs font-bold text-[#111827] mt-1">{{ $internship->division }}</p>
                <p class="text-[11px] text-zinc-700 mt-0.5">{{ $internship->position }}</p>
            </div>
            <div class="bg-[#D4EEFC] rounded-[24px] p-4">
                <p class="text-[11px] text-[#0C4A6E] font-medium flex items-center gap-1">
                    <i class="fa-solid fa-user-tie text-[10px]"></i>
                    <span>Mentor Pembimbing</span>
                </p>
                <p class="text-xs font-bold text-[#111827] mt-1">{{ $internship->mentor?->name ?? '-' }}</p>
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
            <span class="text-[11px] font-medium text-zinc-400">{{ $user->email }}</span>
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

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Nama Mentor</label>
            <input type="text" name="mentor_name" value="{{ $internship?->mentor?->name ?? ($user->preferences['mentor_name'] ?? '-') }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
        </div>

        <button type="submit" class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-xs font-bold py-3 rounded-full flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Simpan Data Peserta</span>
        </button>
    </form>

    <!-- Tambah Template Catatan Harian (Disabled / Non-Creatable + Labeled as Fitur Lanjutan) -->
    <section onclick="openAdvancedFeatureModal('template')"
             class="bg-white rounded-[28px] p-5 space-y-3 shadow-2xs relative cursor-pointer border-2 border-dashed border-[#C8EBE5] hover:border-[#0B6E6C] transition">
        <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-[#111827] flex items-center gap-1.5">
                <i class="fa-solid fa-layer-group text-[#0B6E6C]"></i>
                <span>Tambah Template Catatan Harian</span>
            </h2>
            <span class="bg-[#FDE9B4] text-[#78350F] text-[10px] font-bold px-2.5 py-1 rounded-full inline-flex items-center gap-1 shrink-0">
                <i class="fa-solid fa-lock text-[9px]"></i>
                <span>Fitur Lanjutan</span>
            </span>
        </div>

        <p class="text-[11px] text-zinc-500">
            Fitur pembuatan template otomatis sedang dalam tahap pengembangan lanjutan.
        </p>

        <input type="text" readonly tabindex="-1"
               placeholder="Judul template (misal: Rekapitulasi Data Harian)"
               class="w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs text-zinc-400 pointer-events-none select-none">

        <textarea rows="2" readonly tabindex="-1"
                  placeholder="Deskripsi kegiatan harian default..."
                  class="w-full rounded-[14px] bg-[#F4F7F6] border-0 p-3 text-xs text-zinc-400 pointer-events-none select-none"></textarea>

        <div class="flex items-center justify-between pt-1">
            <button type="button"
                    onclick="event.stopPropagation(); openAdvancedFeatureModal('template')"
                    class="bg-[#C8EBE5] hover:bg-[#0B6E6C] text-[#074745] hover:text-white text-xs font-bold px-4 py-2.5 rounded-full inline-flex items-center gap-1.5 transition">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Simpan Template</span>
                <span class="bg-white/90 text-[#0B6E6C] text-[9px] font-bold px-2 py-0.5 rounded-full ml-1">Segera Hadir</span>
            </button>
        </div>
    </section>

    <!-- Logout / Ganti Akun Card -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
                class="w-full bg-white hover:bg-[#FCE1D4] text-[#9A3412] text-xs font-bold py-3.5 rounded-full shadow-2xs flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Keluar dari Akun ({{ $user->email }})</span>
        </button>
    </form>

</div>
@endsection
