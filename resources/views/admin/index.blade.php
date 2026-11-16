@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-8 space-y-4">

    <header class="bg-lavender-grid rounded-[28px] p-5 shadow-2xs">
        <span class="bg-white px-3 py-1 rounded-full text-[11px] font-bold text-[#18181B]">
            Panel Administrator
        </span>
        <h1 class="text-[20px] font-semibold text-[#111827] mt-2">Manajemen Instansi, Peserta & AI</h1>
        <p class="text-xs text-zinc-600 mt-0.5">Kelola pengguna, kuota harian Gemini, dan rekap kepatuhan global</p>
    </header>

    <div class="grid grid-cols-2 gap-3">
        <div class="bg-[#D5EDE8] rounded-[24px] p-4">
            <p class="text-xs text-zinc-600">Total Entri Logbook</p>
            <p class="text-2xl font-bold text-[#111827] mt-1">{{ $totalLogs }}</p>
        </div>
        <div class="bg-[#FBE8A6] rounded-[24px] p-4">
            <p class="text-xs text-zinc-600">Panggilan AI Hari Ini</p>
            <p class="text-2xl font-bold text-[#111827] mt-1">{{ $aiStats['today_calls'] }} / {{ $aiStats['daily_limit'] }}</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.save') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3 shadow-2xs">
        @csrf
        <h2 class="text-sm font-semibold text-[#111827]">Pengaturan Sistem & Kuota Gemini</h2>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Nama Instansi / Kop Laporan</label>
            <input type="text" name="institution_name" value="{{ $institutions->first()?->name ?? 'PT Kinra Digital Nusantara' }}" required
                   class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2 text-xs font-semibold">
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Batas Backdate (Hari)</label>
                <input type="number" name="max_backdate_days" value="7" required
                       class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2 text-xs font-semibold">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Kuota AI / User / Hari</label>
                <input type="number" name="ai_daily_limit" value="{{ $aiStats['daily_limit'] }}" required
                       class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2 text-xs font-semibold">
            </div>
        </div>

        <button type="submit" class="w-full bg-[#18181B] text-white text-xs font-bold py-3 rounded-full">
            Simpan Pengaturan Admin
        </button>
    </form>

    <section class="bg-white rounded-[28px] p-5 space-y-3 shadow-2xs">
        <h2 class="text-sm font-semibold text-[#111827]">Daftar Pengguna ({{ $users->count() }})</h2>
        @foreach ($users as $u)
            <div class="flex items-center justify-between py-2 border-b border-zinc-100 last:border-0">
                <div>
                    <p class="text-xs font-semibold text-[#111827]">{{ $u->name }}</p>
                    <p class="text-[11px] text-zinc-400">{{ $u->email }}</p>
                </div>
                <span class="bg-[#E5D9FA] text-[#18181B] text-[10px] font-bold px-2.5 py-1 rounded-full">
                    {{ $u->role->label() }}
                </span>
            </div>
        @endforeach
    </section>

</div>
@endsection
