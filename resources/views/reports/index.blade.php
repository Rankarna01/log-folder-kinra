@extends('layouts.app')

@section('content')
<div class="px-5 pt-5 pb-8 space-y-4">

    <header class="bg-lavender-grid rounded-[28px] p-5 shadow-2xs">
        <span class="bg-white px-3 py-1 rounded-full text-[11px] font-bold text-[#18181B]">
            📄 Laporan & Ekspor PDF
        </span>
        <h1 class="text-[20px] font-semibold text-[#111827] mt-2">Rekap Kegiatan & AI Summary</h1>
        <p class="text-xs text-zinc-600 mt-0.5">Cetak laporan harian, mingguan, bulanan, atau laporan akhir lengkap dengan QR Verifikasi</p>
    </header>

    <!-- AI Monthly Summary & Skill Extraction Card -->
    @if ($aiSummary)
        <section class="bg-white rounded-[28px] p-5 space-y-3 shadow-2xs">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-[#111827]">✨ Ringkasan AI Bulanan & Ekstraksi Skill</h2>
                <span class="bg-[#E5D9FA] text-[#18181B] text-[10px] font-bold px-2.5 py-0.5 rounded-full">Gemini Flash</span>
            </div>
            <p class="text-xs text-zinc-700 leading-relaxed">{{ $aiSummary['overview'] ?? '' }}</p>

            @if (!empty($aiSummary['skills']))
                <div>
                    <p class="text-[11px] font-semibold text-zinc-400 mb-1.5">Kompetensi yang Diperoleh:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($aiSummary['skills'] as $skill)
                            <span class="bg-[#D5EDE8] text-[#18181B] text-[11px] font-semibold px-3 py-1 rounded-full">
                                ✓ {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    <!-- Form Generate PDF (No Admin Approval Filter) -->
    <form action="{{ route('reports.store') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-3.5 shadow-2xs">
        @csrf
        <h2 class="text-sm font-semibold text-[#111827]">Buat Laporan PDF Baru</h2>

        <div>
            <label class="block text-xs font-semibold text-zinc-500">Jenis Laporan</label>
            <select name="type" class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3.5 py-2.5 text-xs font-semibold text-[#111827]">
                <option value="monthly">Laporan Bulanan (Rekap + AI Summary)</option>
                <option value="weekly">Laporan Mingguan (Senin - Jumat)</option>
                <option value="daily">Laporan Harian</option>
                <option value="final">Laporan Akhir Magang</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Dari Tanggal</label>
                <input type="date" name="period_start" value="2026-11-01" required
                       class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3 py-2 text-xs font-semibold text-[#111827]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-500">Sampai Tanggal</label>
                <input type="date" name="period_end" value="2026-11-30" required
                       class="mt-1 w-full rounded-[14px] bg-[#F6F7F9] border-0 px-3 py-2 text-xs font-semibold text-[#111827]">
            </div>
        </div>

        <div class="space-y-2 pt-1">
            <label class="flex items-center gap-2 text-xs text-zinc-700">
                <input type="checkbox" name="include_ai_summary" value="1" checked class="rounded text-[#18181B]">
                <span>Sertakan Ringkasan Narasi AI Gemini</span>
            </label>
            <label class="flex items-center gap-2 text-xs text-zinc-700">
                <input type="checkbox" name="include_photos" value="1" checked class="rounded text-[#18181B]">
                <span>Sertakan Lampiran Dokumentasi & Tanda Tangan</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-[#18181B] text-white text-xs font-bold py-3.5 rounded-full shadow-md">
            🖨️ Generate & Cetak Laporan PDF
        </button>
    </form>

    <!-- Generated Reports History -->
    <section class="space-y-2.5">
        <h2 class="text-sm font-semibold text-[#111827]">Riwayat Laporan Tersedia ({{ $reports->count() }})</h2>
        @foreach ($reports as $rep)
            <div class="bg-[#D5EDE8] rounded-[24px] p-4 flex items-center justify-between">
                <div>
                    <span class="bg-white px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase text-[#18181B]">
                        {{ $rep->type }}
                    </span>
                    <p class="text-xs font-semibold text-[#111827] mt-1">
                        Periode {{ $rep->period_start->format('d M Y') }} – {{ $rep->period_end->format('d M Y') }}
                    </p>
                    <p class="text-[11px] text-zinc-600">Token QR: {{ $rep->verify_token }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <a href="{{ route('reports.download', $rep) }}"
                       class="bg-[#18181B] text-white text-xs font-semibold px-3.5 py-1.5 rounded-full text-center">
                        Buka PDF
                    </a>
                    <a href="{{ route('reports.verify', $rep->verify_token) }}"
                       class="bg-white text-[#18181B] text-[11px] font-semibold px-3 py-1 rounded-full text-center">
                        Cek QR
                    </a>
                </div>
            </div>
        @endforeach
    </section>

</div>
@endsection
