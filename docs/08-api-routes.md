# 08. Route dan Endpoint

Mayoritas halaman lewat Livewire. Endpoint JSON dipakai untuk AI, upload, sinkron offline, dan PWA.

## Web (Livewire / Blade)

| Method | URI | Peran | Keterangan |
|--------|-----|-------|-----------|
| GET | `/login`, `/register` | guest | Auth |
| GET | `/dashboard` | semua | Beranda sesuai peran |
| GET | `/logbook` | peserta | Daftar |
| GET | `/logbook/create` | peserta | Form tambah |
| GET | `/logbook/{id}` | peserta/mentor | Detail |
| GET | `/logbook/{id}/edit` | peserta | Edit |
| GET | `/reports` | peserta/mentor | Daftar laporan |
| GET | `/reports/create` | peserta/mentor | Form buat laporan |
| GET | `/reports/{id}/download` | pemilik/mentor | Unduh PDF |
| GET | `/review` | mentor | Antrean review |
| GET | `/mentor/participants` | mentor | Peserta bimbingan |
| GET | `/admin/users` | admin | Kelola user |
| GET | `/admin/institutions` | admin | Instansi dan periode |
| GET | `/admin/recap` | admin | Rekap global |
| GET | `/admin/settings` | admin | Pengaturan |
| GET | `/profile` | semua | Profil |
| GET | `/verify/{token}` | publik | Verifikasi PDF |

## JSON Endpoint (`/api/...`, middleware `auth` session/Sanctum)

### Logbook
| Method | URI | Keterangan |
|--------|-----|-----------|
| POST | `/api/logbooks` | Buat entri (mendukung `client_uuid`) |
| PUT | `/api/logbooks/{id}` | Ubah |
| DELETE | `/api/logbooks/{id}` | Hapus (soft) |
| POST | `/api/logbooks/{id}/submit` | Kirim untuk review |
| POST | `/api/logbooks/sync` | Sinkron batch dari offline |
| POST | `/api/logbooks/{id}/attachments` | Upload foto (multipart) |
| DELETE | `/api/attachments/{id}` | Hapus foto |

### Review (mentor)
| Method | URI | Keterangan |
|--------|-----|-----------|
| POST | `/api/logbooks/{id}/approve` | Approve |
| POST | `/api/logbooks/{id}/revision` | Minta revisi (body: message) |
| POST | `/api/logbooks/{id}/comments` | Komentar |
| POST | `/api/review/bulk-approve` | Approve massal |

### AI (throttle + kuota)
| Method | URI | Body | Respons |
|--------|-----|------|---------|
| POST | `/api/ai/polish` | `text`, `style` | `{ result }` |
| POST | `/api/ai/transcribe` | `audio` (file), `polish` (bool) | `{ transcript, polished? }` |
| POST | `/api/ai/photo-suggest` | `attachment_id` / `image` | `{ caption, suggestion }` |
| POST | `/api/ai/summary` | `type`, `start`, `end` | `{ report_id }` (async) |
| GET | `/api/ai/quota` | | `{ used, limit }` |

### Laporan
| Method | URI | Keterangan |
|--------|-----|-----------|
| POST | `/api/reports` | Buat laporan (async), balas `report_id` |
| GET | `/api/reports/{id}/status` | Polling status `queued/processing/ready/failed` |

### PWA / Push
| Method | URI | Keterangan |
|--------|-----|-----------|
| POST | `/api/push/subscribe` | Simpan subscription |
| DELETE | `/api/push/unsubscribe` | Hapus |

## Contoh Kontrak

**POST /api/ai/polish**
```json
// request
{ "text": "bikin laporan, rapihin data excel", "style": "formal" }
// response 200
{ "result": "Menyusun laporan dan merapikan data pada Microsoft Excel.",
  "quota": { "used": 4, "limit": 30 } }
// response 429
{ "message": "Kuota AI harian habis." }
```

**POST /api/logbooks/sync**
```json
{
  "items": [
    { "client_uuid": "…", "date": "2026-11-16", "start_time": "08:00",
      "end_time": "10:00", "title": "…", "raw_description": "…" }
  ]
}
// response
{ "synced": [{ "client_uuid": "…", "id": 123 }], "failed": [] }
```

## Standar
- Validasi via `FormRequest`, error format Laravel (422)
- Otorisasi via Policy di setiap endpoint
- Rate limit: AI `10/menit`, upload `30/menit`, login `5/menit`
- Semua waktu disimpan UTC atau `Asia/Jakarta` secara konsisten (set di `config/app.php`)
