@extends('layouts.app')

@section('content')
<div class="px-5 pt-6 pb-8 space-y-5">

    <!-- Hero KINRA Deep Green Grid Onboarding Card (Step 2 of 2) -->
    <section class="bg-kinra-grid rounded-[28px] p-6 shadow-xs relative overflow-hidden text-white">
        <div class="flex items-center justify-between gap-2">
            <div class="bg-white rounded-2xl px-3.5 py-2 shadow-xs inline-flex items-center">
                <img src="/images/kinra.png" alt="Logo KINRA Sei Mangkei" class="h-7 w-auto object-contain">
            </div>
            <span class="inline-flex items-center gap-1.5 bg-white/95 px-3 py-1 rounded-full text-[11px] font-bold text-[#0B6E6C] shadow-2xs">
                <i class="fa-solid fa-user-check"></i>
                <span>Data Peserta</span>
            </span>
        </div>

        <h1 class="text-[22px] font-bold text-white mt-4 leading-snug">
            Lengkapi Data Peserta Magang
        </h1>
        <p class="text-xs text-emerald-100 mt-1 leading-relaxed">
            Akun <strong>{{ $user?->email }}</strong> sudah aktif! Sekarang isi nama, divisi, bagian, dan mentor untuk mulai mencatat logbook harian.
        </p>
    </section>

    <!-- Step 2: 4-Field Participant Onboarding Form -->
    <form action="{{ route('onboarding.store') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-4 shadow-2xs">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-[#111827]">1. Nama Lengkap Peserta</label>
            <input type="text" id="name" name="name" value="{{ old('name', $internship ? $user?->name : '') }}" required autofocus
                   placeholder="Contoh: Muhammad Randy Karna"
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <div>
            <label for="division" class="block text-xs font-semibold text-[#111827]">2. Divisi</label>
            <input type="text" id="division" name="division" value="{{ old('division', $internship?->division) }}" required
                   placeholder="Contoh: IT & Sistem Informasi / Operasional / SDM"
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <div>
            <label for="position" class="block text-xs font-semibold text-[#111827]">3. Bagian / Posisi Magang</label>
            <input type="text" id="position" name="position" value="{{ old('position', $internship?->position) }}" required
                   placeholder="Contoh: Full Stack Developer / Administrasi"
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <div>
            <label for="mentor_name" class="block text-xs font-semibold text-[#111827]">4. Nama Mentor Pembimbing</label>
            <input type="text" id="mentor_name" name="mentor_name" value="{{ old('mentor_name', $internship?->mentor?->name) }}" required
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <button type="submit"
                class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-sm font-bold py-3.5 rounded-full shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2">
            <span>Simpan & Masuk ke Beranda</span>
            <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>

</div>
@endsection
