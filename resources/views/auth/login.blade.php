@extends('layouts.app')

@section('content')
<div class="px-5 pt-6 pb-8 space-y-5">

    <!-- Hero KINRA Deep Green Grid Card + Official Logo -->
    <section class="bg-kinra-grid rounded-[28px] p-6 shadow-xs relative overflow-hidden text-white">
        <div class="flex items-center justify-between gap-2">
            <div class="bg-white rounded-2xl px-3.5 py-2 shadow-xs inline-flex items-center">
                <img src="/images/kinra.png" alt="Logo KINRA Sei Mangkei" class="h-7 w-auto object-contain">
            </div>
            <span class="inline-flex items-center gap-1.5 bg-white/95 px-3 py-1 rounded-full text-[11px] font-bold text-[#0B6E6C] shadow-2xs">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk Akun Peserta</span>
            </span>
        </div>

        <h1 class="text-[22px] font-bold text-white mt-4 leading-snug">
            Selamat Datang Kembali
        </h1>
    </section>

    <!-- Segmented Switcher: Masuk Akun vs Daftar Akun Baru -->
    <div class="bg-[#E6ECEB] p-1.5 rounded-full grid grid-cols-2 gap-1">
        <a href="{{ route('login') }}"
           class="bg-white text-[#0B6E6C] font-bold text-xs py-2.5 rounded-full shadow-xs text-center flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>Masuk (Login)</span>
        </a>
        <a href="{{ route('register') }}"
           class="text-zinc-600 hover:text-[#111827] font-semibold text-xs py-2.5 rounded-full text-center flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-user-plus"></i>
            <span>Daftar Akun Baru</span>
        </a>
    </div>

    <!-- Login Form (Email + Password) -->
    <form action="{{ route('login.submit') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-4 shadow-2xs">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-[#111827]">Email Akun</label>
            <div class="relative mt-1.5">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-zinc-400">
                    <i class="fa-regular fa-envelope text-sm"></i>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="nama@email.com"
                       class="w-full rounded-[16px] bg-[#F4F7F6] border-0 pl-10 pr-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
            </div>
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-[#111827]">Password</label>
            <div class="relative mt-1.5">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-zinc-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input type="password" id="password" name="password" required
                       placeholder="Masukkan password akunmu"
                       class="w-full rounded-[16px] bg-[#F4F7F6] border-0 pl-10 pr-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
            </div>
        </div>

        <button type="submit"
                class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-sm font-bold py-3.5 rounded-full shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2">
            <span>Masuk ke Logbook Harian</span>
            <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>

</div>
@endsection
