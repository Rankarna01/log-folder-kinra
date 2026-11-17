@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-8 space-y-4">

    <header class="bg-kinra-grid rounded-[28px] p-5 shadow-2xs text-white">
        <div class="flex items-center justify-between">
            <span class="bg-white px-3 py-1 rounded-full text-[11px] font-bold text-[#0B6E6C] flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Laporan & Ekspor PDF</span>
            </span>
            <div class="bg-white rounded-xl px-2.5 py-1">
                <img src="/images/kinra.png" alt="KINRA" class="h-4 w-auto object-contain">
            </div>
        </div>
        <h1 class="text-[20px] font-semibold text-white mt-3">Rekap Kegiatan Harian & Bulanan</h1>
        <p class="text-xs text-emerald-100 mt-0.5">Cetak laporan harian, mingguan, bulanan, atau laporan akhir lengkap dengan Kop KINRA & QR Verifikasi</p>
    </header>

    <!-- AI Summary Preview Banner -> Triggers Advanced Feature Modal -->
    <!-- <section class="bg-white rounded-[28px] p-5 space-y-3 shadow-2xs">
        <div class="flex items-center justify-between gap-2">
            <div>
                <span class="bg-[#C8EBE5] text-[#074745] text-[10px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Fitur Lanjutan</span>
                </span>
                <h2 class="text-sm font-semibold text-[#111827] mt-1.5">Ringkasan Otomatis AI & Ekstraksi Skill</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Buat narasi capaian bulanan dan daftar kompetensi otomatis dengan AI Gemini.</p>
            </div>
            <button type="button"
                    onclick="openAdvancedFeatureModal('summary')"
                    class="shrink-0 bg-[#0B6E6C] text-white text-xs font-semibold px-3.5 py-2 rounded-full shadow-xs hover:opacity-90 transition flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info"></i>
                <span>Lihat Info</span>
            </button>
        </div>
    </section> -->

    <!-- Form Generate PDF -->
    <form action="{{ route('reports.store') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3.5 shadow-2xs">
        @csrf
        <h2 class="text-sm font-semibold text-[#111827]">Buat Laporan PDF Baru</h2>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Jenis Laporan</label>
            <select name="type" class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
                <option value="monthly">Laporan Bulanan</option>
                <option value="weekly">Laporan Mingguan (Senin - Jumat)</option>
                <option value="daily">Laporan Harian</option>
                <option value="final">Laporan Akhir Magang</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Dari Tanggal</label>
                <input type="date" name="period_start" value="2026-11-01" required
                       class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3 py-2 text-xs font-semibold text-[#111827]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Sampai Tanggal</label>
                <input type="date" name="period_end" value="2026-11-30" required
                       class="mt-1 w-full rounded-[14px] bg-[#F4F7F6] border-0 px-3 py-2 text-xs font-semibold text-[#111827]">
            </div>
        </div>

        <div class="space-y-2 pt-1">
            <label class="flex items-center gap-2 text-xs text-zinc-700">
                <input type="checkbox" name="include_photos" value="1" checked class="rounded text-[#0B6E6C]">
                <span>Sertakan Lampiran Dokumentasi & Kolom Tanda Tangan</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-xs font-bold py-3.5 rounded-full shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Generate & Cetak Laporan PDF</span>
        </button>
    </form>

    <!-- Generated Reports History -->
    <section class="space-y-2.5">
        <h2 class="text-sm font-semibold text-[#111827]">Riwayat Laporan Tersedia ({{ $reports->count() }})</h2>
        @foreach ($reports as $rep)
            <div class="bg-[#C8EBE5] rounded-[24px] p-4 flex items-center justify-between">
                <div>
                    <span class="bg-white px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase text-[#0B6E6C]">
                        {{ $rep->type }}
                    </span>
                    <p class="text-xs font-semibold text-[#111827] mt-1">
                        Periode {{ $rep->period_start->format('d M Y') }} – {{ $rep->period_end->format('d M Y') }}
                    </p>
                    <p class="text-[11px] text-zinc-600">Token QR: {{ $rep->verify_token }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <a href="{{ route('reports.download', $rep) }}"
                       class="bg-[#0B6E6C] text-white text-xs font-semibold px-3.5 py-1.5 rounded-full text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-file-pdf text-[11px]"></i>
                        <span>Buka PDF</span>
                    </a>
                    <a href="{{ route('reports.verify', $rep->verify_token) }}"
                       class="bg-white text-[#0B6E6C] text-[11px] font-semibold px-3 py-1 rounded-full text-center flex items-center justify-center gap-1">
                        <i class="fa-solid fa-qrcode text-[10px]"></i>
                        <span>Cek QR</span>
                    </a>
                </div>
            </div>
        @endforeach
    </section>

</div>
@endsection
