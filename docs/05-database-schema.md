# 05. Skema Database

## ERD (ringkas)

```
users 1───* internships *───1 institutions
  │              │
  │              └──* logbooks 1───* attachments
  │                       │
  │                       └───* comments
  │
  ├──* reports
  ├──* ai_usages
  ├──* push_subscriptions
  └──* activity_templates
```

## Tabel

### users
| Kolom | Tipe | Catatan |
|-------|------|---------|
| id | bigint PK | |
| name | varchar | |
| email | varchar unique | |
| password | varchar | |
| role | enum(`participant`,`mentor`,`admin`) | index |
| phone | varchar null | |
| nim_nik | varchar null | |
| campus | varchar null | |
| major | varchar null | |
| avatar_path | varchar null | |
| signature_path | varchar null | |
| preferences | json null | gaya AI, bahasa, jam pengingat |
| is_active | boolean | default true |
| timestamps, softDeletes | | |

### institutions
| id | name | address | logo_path | letterhead (json) | timestamps |

### internships
| Kolom | Tipe | Catatan |
|-------|------|---------|
| id | bigint PK | |
| participant_id | FK users | index |
| mentor_id | FK users null | index |
| institution_id | FK | |
| division | varchar | |
| position | varchar null | |
| start_date, end_date | date | |
| work_days | json | mis. [1,2,3,4,5] |
| status | enum(`active`,`finished`,`cancelled`) | |

### logbooks
| Kolom | Tipe | Catatan |
|-------|------|---------|
| id | bigint PK | |
| internship_id | FK | |
| date | date | **index** |
| start_time, end_time | time | |
| duration_minutes | int | dihitung otomatis |
| title | varchar(200) | |
| category | varchar / enum | |
| raw_description | text | teks asli peserta |
| ai_description | text null | hasil rapikan AI |
| final_description | text | yang dipakai di laporan |
| output | text null | |
| obstacles | text null | |
| work_location | enum(`wfo`,`wfh`,`field`) null | |
| lat, lng | decimal null | opsional geotag |
| status | enum(`draft`,`submitted`,`approved`,`revision`) | **index** |
| submitted_at, reviewed_at | datetime null | |
| reviewed_by | FK users null | |
| source | enum(`manual`,`voice`,`offline_sync`) | |
| client_uuid | uuid unique | cegah duplikat saat sinkron offline |
| timestamps, softDeletes | | |

Index gabungan: `(internship_id, date)`, `(internship_id, status)`.

### attachments
| id | logbook_id FK | path | thumb_path | mime | size_kb | caption | width | height | taken_at | sort_order | timestamps |

### comments
| id | logbook_id FK | user_id FK | message text | type enum(`comment`,`revision_request`,`approval`) | timestamps |

### reports
| Kolom | Tipe | Catatan |
|-------|------|---------|
| id | PK | |
| internship_id | FK | |
| type | enum(`daily`,`weekly`,`monthly`,`custom`,`final`) | |
| period_start, period_end | date | |
| options | json | sertakan foto, ringkasan AI, dll. |
| ai_summary | longtext null | |
| pdf_path | varchar null | |
| status | enum(`queued`,`processing`,`ready`,`failed`) | |
| verify_token | varchar unique | untuk QR verifikasi |
| generated_at | datetime null | |
| timestamps | | |

### ai_usages
| id | user_id | feature enum(`polish`,`transcribe`,`photo`,`summary`,`skills`,`final_report`) | model | input_tokens | output_tokens | status | created_at |

Dipakai untuk kuota harian dan monitoring biaya.

### activity_templates
| id | user_id | title | category | description | timestamps |

### push_subscriptions
Mengikuti skema package `webpush`.

### notifications
Tabel bawaan Laravel.

### settings
| key (unique) | value (json) |

### holidays
| id | institution_id null | date | name |

## Enum Status Logbook

```
draft ──submit──► submitted ──approve──► approved
                      │
                      └──request revision──► revision ──edit+submit──► submitted
```

## Catatan Performa
- Index pada `logbooks.date`, `logbooks.status`, `internship_id`
- Eager loading: `with(['attachments','comments.user'])`
- Hindari menyimpan blob di DB, simpan hanya path
- Archive/soft delete, jangan hard delete entri yang sudah approved
