@extends('layouts.app')

@section('content')
<div class="px-5 pt-6 pb-8 space-y-5">

    <div class="bg-lavender-grid rounded-[28px] p-6 text-center space-y-2 shadow-xs">
        <span class="bg-white px-3 py-1 rounded-full text-xs font-bold text-[#18181B]">Timesheet MagangHub PWA</span>
        <h1 class="text-xl font-bold text-[#111827] pt-1">Masuk ke Akun MagangHub</h1>
        <p class="text-xs text-zinc-600">Catat kegiatan harian, rapikan dengan AI Gemini, dan cetak laporan PDF</p>
    </div>

    <form action="{{ route('login.submit') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3.5 shadow-2xs">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-[#111827]">Email</label>
            <input type="email" name="email" value="peserta@maganghub.id" required
                   class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs font-semibold">
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#111827]">Password</label>
            <input type="password" name="password" value="password" required
                   class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs font-semibold">
        </div>
        <button type="submit" class="w-full bg-[#18181B] text-white text-xs font-bold py-3.5 rounded-full">
            Masuk Sekarang →
        </button>
    </form>

    <div class="bg-[#D5EDE8] rounded-[26px] p-4 space-y-2">
        <p class="text-xs font-bold text-[#111827]">Akun Demo Siap Pakai (Klik 1-Tap):</p>
        <div class="grid grid-cols-3 gap-2">
            <form action="{{ route('role.switch', 'participant') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-white text-[#18181B] text-xs font-bold py-2 rounded-full">Peserta</button>
            </form>
            <form action="{{ route('role.switch', 'mentor') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-white text-[#18181B] text-xs font-bold py-2 rounded-full">Mentor</button>
            </form>
            <form action="{{ route('role.switch', 'admin') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-white text-[#18181B] text-xs font-bold py-2 rounded-full">Admin</button>
            </form>
        </div>
    </div>

</div>
@endsection
