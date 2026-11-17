<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Magang — {{ $participant->name }} ({{ $report->period_start->format('d/m/Y') }} - {{ $report->period_end->format('d/m/Y') }})</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #18181B;
            background: #D9E5E4;
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
            background: #0B6E6C;
            color: #fff;
            padding: 12px 20px;
            border-radius: 999px;
        }
        .toolbar a, .toolbar button {
            background: #FDE9B4;
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
            border-bottom: 3px solid #0B6E6C;
            padding-bottom: 12px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .letterhead h1 { margin: 0; font-size: 17px; text-transform: uppercase; color: #0B6E6C; }
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
            background: #F4F7F6;
            border-left: 4px solid #0B6E6C;
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
            background: #0B6E6C;
            color: #FFFFFF;
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
            <strong>Timesheet KINRA Sei Mangkei — Pratinjau Cetak PDF A4</strong>
            <span style="margin-left: 8px; opacity: 0.85;">({{ $entries->count() }} Entri • {{ $totalHours }} Jam)</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('reports.index') }}" style="background: #fff;">← Kembali</a>
            <button onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    <div class="page">
        <div class="letterhead">
            <div style="display: flex; align-items: center; gap: 14px;">
                <img src="/images/kinra.png" alt="KINRA Sei Mangkei" style="height: 48px; width: auto;">
                <div>
                    <h1>{{ $institution->name }}</h1>
                    <p>{{ $institution->address }}</p>
                    <p><strong>LAPORAN KEGIATAN HARIAN PESERTA MAGANG ({{ strtoupper($report->type) }})</strong></p>
                </div>
            </div>
        </div>

        <table class="identity-table">
            <tr>
                <td width="18%"><strong>Nama Peserta</strong></td>
                <td width="32%">: {{ $participant->name }}</td>
                <td width="18%"><strong>Divisi</strong></td>
                <td width="32%">: {{ $internship->division }}</td>
            </tr>
            <tr>
                <td><strong>Bagian / Posisi</strong></td>
                <td>: {{ $internship->position ?? '-' }}</td>
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
                <strong>Ringkasan Eksekutif:</strong>
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
                    <th width="16%">Tanggal & Jam</th>
                    <th width="24%">Judul Kegiatan</th>
                    <th width="35%">Deskripsi Kegiatan</th>
                    <th width="12%">Output / Hasil</th>
                    <th width="8%">Lokasi</th>
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
                            <strong>{{ $entry->title }}</strong>
                        </td>
                        <td>{{ $entry->final_description }}</td>
                        <td>
                            @if (!empty($entry->output))
                                @php
                                    $outputTrimmed = trim($entry->output);
                                    $isUrl = preg_match('~^(https?://|www\.)~i', $outputTrimmed);
                                    $href = $isUrl ? (str_starts_with($outputTrimmed, 'http') ? $outputTrimmed : 'https://' . $outputTrimmed) : null;
                                @endphp
                                @if ($href)
                                    <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" style="color: #0B6E6C; font-weight: bold; text-decoration: underline; word-break: break-all;">
                                        🔗 Buka Tautan Output
                                    </a>
                                @else
                                    {{ $entry->output }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td><strong>{{ $entry->location_label }}</strong></td>
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
                    <p><strong><u>{{ $participant->name }}</u></strong><br>{{ $internship->position ?? 'Peserta Magang' }}</p>
                </td>
                <td>
                    <p>Mengetahui, Mentor Pembimbing</p>
                    <br><br><br>
                    <p><strong><u>{{ $mentor?->name ?? '-' }}</u></strong><br>{{ $internship->division }}</p>
                </td>
            </tr>
        </table>

        <div style="margin-top: 24px; border-top: 1px dashed #D4D4D8; padding-top: 10px; font-size: 10px; color: #71717A; display: flex; justify-content: space-between;">
            <span>Dicetak otomatis melalui Timesheet KINRA Sei Mangkei PWA pada {{ now()->format('d/m/Y H:i') }}</span>
            <span>Verifikasi keaslian: <a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></span>
        </div>
    </div>

</body>
</html>
