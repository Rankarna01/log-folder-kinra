# 02. Arsitektur Sistem

## Gaya Arsitektur
**Monolith modular** berbasis Laravel. Dipilih karena target deployment shared hosting (satu aplikasi PHP, tanpa proses Node.js permanen, tanpa Docker).

## Diagram Tingkat Tinggi

```
┌────────────────────────────────────────────────────────┐
│                   Perangkat Pengguna                   │
│  Browser/PWA (Blade + Livewire + Alpine + Tailwind)    │
│  Service Worker • IndexedDB • MediaRecorder • Camera   │
└───────────────────────┬────────────────────────────────┘
                        │ HTTPS
┌───────────────────────▼────────────────────────────────┐
│                  Laravel Application                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ Livewire /   │  │ Middleware   │  │ Policies     │  │
│  │ Controllers  │  │ (auth, role) │  │ (akses data) │  │
│  └──────┬───────┘  └──────────────┘  └──────────────┘  │
│         ▼                                              │
│  ┌──────────────────────────────────────────────────┐  │
│  │ Service Layer                                    │  │
│  │ LogbookService • AttachmentService               │  │
│  │ GeminiService • ReportService • PdfService       │  │
│  └──────┬──────────────────────┬────────────────────┘  │
│         ▼                      ▼                       │
│  ┌──────────────┐      ┌──────────────────┐            │
│  │ Eloquent     │      │ Jobs (queue DB)  │            │
│  │ Models       │      │ AI • PDF • Push  │            │
│  └──────┬───────┘      └────────┬─────────┘            │
└─────────┼───────────────────────┼──────────────────────┘
          ▼                       ▼
   ┌────────────┐        ┌──────────────────┐
   │ MySQL      │        │ Gemini API       │
   └────────────┘        │ (Google, HTTPS)  │
   ┌────────────┐        └──────────────────┘
   │ Storage    │  foto, audio sementara, PDF
   │ (local/R2) │
   └────────────┘
```

## Layer dan Tanggung Jawab

| Layer | Tanggung jawab |
|-------|----------------|
| **Presentation** | Blade, Livewire component, Alpine, Tailwind |
| **HTTP** | Controller tipis, FormRequest untuk validasi, middleware role |
| **Authorization** | Policy per model (peserta hanya akses datanya, mentor hanya bimbingannya) |
| **Service** | Logika bisnis (LogbookService, GeminiService, PdfService, dst.) |
| **Data** | Eloquent model, query scope, relasi |
| **Async** | Job queue (driver `database`) dijalankan cron |
| **External** | Gemini API, Web Push, opsional Cloudflare R2 |

## Alur Utama

### A. Input Logbook dengan AI
```
Peserta isi form (teks kasar / suara)
   → [jika suara] audio → GeminiService::transcribe()
   → GeminiService::polish(teks, gaya)
   → Peserta lihat hasil: Terapkan / Edit / Abaikan
   → Simpan logbook (raw_description + ai_description)
   → Upload foto (kompres client-side → AttachmentService → thumbnail)
```

### B. Ekspor PDF
```
Pilih periode → ReportService kumpulkan data
   → dispatch GeneratePdfJob
   → PdfService render Blade → DomPDF
   → simpan ke storage, catat di tabel reports
   → notifikasi "PDF siap diunduh"
```

### C. Review Mentor
```
Peserta submit → status "submitted" → notifikasi ke mentor
Mentor: approve / minta revisi + komentar
→ status "approved" / "revision" → notifikasi ke peserta
```

### D. Offline Sync
```
Offline: entri disimpan di IndexedDB (status "pending_sync")
Online kembali: Background Sync / event "online" → POST antrean ke server
Server balas id → tandai synced
```

## Struktur Folder Laravel

```
app/
├── Enums/                 # LogStatus, Role, LogCategory
├── Http/
│   ├── Controllers/
│   ├── Middleware/        # EnsureRole
│   └── Requests/          # StoreLogbookRequest, dst.
├── Jobs/                  # GeneratePdfJob, SummarizeWeekJob, TranscribeAudioJob
├── Livewire/
│   ├── Dashboard/
│   ├── Logbook/           # Index, Form, Show, VoiceRecorder
│   ├── Reports/
│   ├── Mentor/
│   └── Admin/
├── Models/
├── Policies/
├── Services/
│   ├── Ai/GeminiService.php
│   ├── Ai/PromptBuilder.php
│   ├── AttachmentService.php
│   ├── LogbookService.php
│   ├── ReportService.php
│   └── PdfService.php
resources/
├── views/
│   ├── layouts/           # app (mobile-first), guest
│   ├── livewire/
│   └── pdf/               # daily, weekly, monthly, final
├── js/
│   ├── app.js
│   ├── offline/db.js      # Dexie (IndexedDB)
│   ├── voice.js
│   └── image-compress.js
public/
├── manifest.webmanifest
├── sw.js
└── icons/
database/ (migrations, seeders, factories)
docs/
```

## Prinsip Desain
1. **Mobile-first:** semua layar dirancang untuk HP dulu.
2. **Thin controller, fat service:** logika bisnis tidak di controller.
3. **AI sebagai asisten:** hasil AI selalu dikonfirmasi peserta, raw text tetap disimpan.
4. **Graceful degradation:** jika Gemini gagal atau kuota habis, logbook tetap bisa disimpan manual.
5. **Async untuk yang berat:** PDF, ringkasan panjang, transkripsi audio lewat queue.
