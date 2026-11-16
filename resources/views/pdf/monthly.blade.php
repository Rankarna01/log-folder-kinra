<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Magang — {{ $participant->name }} ({{ $report->period_start->format('d/m/Y') }} - {{ $report->period_end->format('d/m/Y') }})</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #18181B;
            background: #E5E7EB;
            margin: 0;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
        }
        .page {
            max-width: 210mm;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 18mm;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            border-radius: 8px;
        }
        .toolbar {
            max-width: 210mm;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #18181B;
            color: #fff;
            padding: 12px 20px;
            border-radius: 999px;
        }
        .toolbar a, .toolbar button {
            background: #E5D9FA;
            color: #18181B;
            border: 0;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
        }
        .letterhead {
            border-bottom: 3px solid #18181B;
            padding-bottom: 12px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .letterhead h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .letterhead p { margin: 2px 0 0; color: #52525B; font-size: 11px; }
        .identity-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .identity-table td {
            padding: 4px 8px;
            font-size: 12px;
        }
        .summary-box {
            background: #F6F7F9;
            border-left: 4px solid #18181B;
            padding: 12px 14px;
            margin-bottom: 16px;
            border-radius: 6px;
        }
        table.activities {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.activities th, table.activities td {
            border: 1px solid #D4D4D8;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        table.activities th {
            background: #E5D9FA;
            font-weight: 700;
            font-size: 11px;
        }
        .signatures {
            width: 100%;
            margin-top: 32px;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; padding: 0; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div>
            <strong>Timesheet MagangHub — Pratinjau Cetak PDF A4</strong>
            <span style="margin-left: 8px; opacity: 0.75;">({{ $entries->count() }} Entri • {{ $totalHours }} Jam)</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('reports.index') }}" style="background: #fff;">← Kembali</a>
            <button onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    <div class="page">
        <div class="letterhead">
            <div>
                <h1>{{ $institution->name }}</h1>
                <p>{{ $institution->address }}</p>
                <p><strong>LAPORAN KEGIATAN MAGANG ({{ strtoupper($report->type) }})</strong></p>
            </div>
            <div style="text-align: right;">
                <div style="background: #18181B; color: #fff; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 11px;">
                    DOKUMEN TERVERIFIKASI
                </div>
                <p style="font-size: 10px; margin-top: 4px;">Token: {{ $report->verify_token }}</p>
            </div>
        </div>

        <table class="identity-table">
            <tr>
                <td width="18%"><strong>Nama Peserta</strong></td>
                <td width="32%">: {{ $participant->name }} ({{ $participant->nim_nik }})</td>
                <td width="18%"><strong>Divisi Penempatan</strong></td>
                <td width="32%">: {{ $internship->division }}</td>
            </tr>
            <tr>
                <td><strong>Asal Kampus</strong></td>
                <td>: {{ $participant->campus }}</td>
                <td><strong>Mentor Pembimbing</strong></td>
                <td>: {{ $mentor?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Periode Laporan</strong></td>
                <td>: {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}</td>
                <td><strong>Total Jam Kerja</strong></td>
                <td>: {{ $totalHours }} Jam ({{ $totalDays }} Hari Aktif)</td>
            </tr>
        </table>

        @if ($summary)
            <div class="summary-box">
                <strong>Ringkasan Eksekutif (AI Gemini):</strong>
                <p style="margin: 4px 0 0;">{{ $summary['overview'] ?? '' }}</p>
                @if (!empty($summary['skills']))
                    <p style="margin: 6px 0 0; font-size: 11px;">
                        <strong>Kompetensi Tercapai:</strong> {{ implode(', ', $summary['skills']) }}
                    </p>
                @endif
            </div>
        @endif

        <table class="activities">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="14%">Tanggal & Jam</th>
                    <th width="22%">Judul & Kategori</th>
                    <th width="36%">Deskripsi Kegiatan</th>
                    <th width="15%">Output / Hasil</th>
                    <th width="8%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $idx => $entry)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $entry->date->format('d/m/Y') }}</strong><br>
                            {{ substr($entry->start_time, 0, 5) }} - {{ substr($entry->end_time, 0, 5) }}<br>
                            <small>({{ $entry->formatted_duration }})</small>
                        </td>
                        <td>
                            <strong>{{ $entry->title }}</strong><br>
                            <small>{{ $entry->category->label() }}</small>
                        </td>
                        <td>{{ $entry->final_description }}</td>
                        <td>{{ $entry->output ?: '-' }}</td>
                        <td><strong>{{ $entry->status->label() }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada entri pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="signatures">
            <tr>
                <td>
                    <p>Peserta Magang,</p>
                    <br><br><br>
                    <p><strong><u>{{ $participant->name }}</u></strong><br>NIM/NIK: {{ $participant->nim_nik }}</p>
                </td>
                <td>
                    <p>Menyetujui, Mentor Pembimbing</p>
                    <br><br><br>
                    <p><strong><u>{{ $mentor?->name ?? 'Agnes Nielsen' }}</u></strong><br>{{ $institution->name }}</p>
                </td>
            </tr>
        </table>

        <div style="margin-top: 24px; border-top: 1px dashed #D4D4D8; padding-top: 10px; font-size: 10px; color: #71717A; display: flex; justify-content: space-between;">
            <span>Dicetak otomatis melalui Timesheet MagangHub PWA pada {{ now()->format('d/m/Y H:i') }}</span>
            <span>Verifikasi keaslian: <a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></span>
        </div>
    </div>

</body>
</html>
