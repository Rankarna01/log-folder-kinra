@extends('layouts.app')

@section('content')
<div class="px-5 pt-8 pb-8 text-center space-y-4">
    <div class="bg-[#FBE8A6] rounded-[28px] p-6 space-y-3">
        <div class="w-12 h-12 rounded-full bg-[#18181B] text-white mx-auto flex items-center justify-center font-bold">
            ⚡
        </div>
        <h1 class="text-lg font-bold text-[#111827]">Anda Sedang Mode Offline</h1>
        <p class="text-xs text-zinc-700">
            Tenang! Anda tetap dapat mengisi draf logbook. Data akan otomatis disinkronkan ke server saat koneksi internet kembali stabil.
        </p>
        <a href="{{ route('logbook.create') }}" class="inline-block bg-[#18181B] text-white text-xs font-semibold px-5 py-2.5 rounded-full">
            + Isi Logbook Offline
        </a>
    </div>
</div>
@endsection
