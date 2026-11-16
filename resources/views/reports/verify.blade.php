@extends('layouts.app')

@section('content')
<div class="px-5 pt-6 pb-8 space-y-4">
    <div class="bg-[#D5EDE8] rounded-[28px] p-6 text-center space-y-3 shadow-xs">
        <div class="w-14 h-14 rounded-full bg-[#18181B] text-white mx-auto flex items-center justify-center text-2xl font-bold">
            ✓
        </div>
        <span class="inline-block bg-white px-3 py-1 rounded-full text-xs font-bold text-emerald-900">
            Dokumen Resmi & Terverifikasi
        </span>
        <h1 class="text-xl font-bold text-[#111827]">Verifikasi Laporan MagangHub</h1>
        <p class="text-xs text-zinc-700">
            Dokumen PDF dengan kode token <strong>{{ $report->verify_token }}</strong> terdaftar sah pada sistem Timesheet MagangHub.
        </p>
    </div>

    <div class="bg-white rounded-[28px] p-5 space-y-3 text-xs shadow-2xs">
        <div class="flex justify-between py-1.5 border-b border-zinc-100">
            <span class="text-zinc-400">Nama Peserta</span>
            <span class="font-semibold text-[#111827]">{{ $report->internship->participant->name }}</span>
        </div>
        <div class="flex justify-between py-1.5 border-b border-zinc-100">
            <span class="text-zinc-400">Instansi</span>
            <span class="font-semibold text-[#111827]">{{ $report->internship->institution->name }}</span>
        </div>
        <div class="flex justify-between py-1.5 border-b border-zinc-100">
            <span class="text-zinc-400">Mentor Pembimbing</span>
            <span class="font-semibold text-[#111827]">{{ $report->internship->mentor?->name ?? '-' }}</span>
        </div>
        <div class="flex justify-between py-1.5 border-b border-zinc-100">
            <span class="text-zinc-400">Periode Laporan</span>
            <span class="font-semibold text-[#111827]">{{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}</span>
        </div>
        <div class="flex justify-between py-1.5">
            <span class="text-zinc-400">Tanggal Diterbitkan</span>
            <span class="font-semibold text-[#111827]">{{ $report->generated_at?->format('d M Y H:i') }} WIB</span>
        </div>
    </div>
</div>
@endsection
