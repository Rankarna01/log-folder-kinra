<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#18181B">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>{{ $title ?? 'Timesheet MagangHub — Logbook Harian Peserta' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        canvas: '#F6F7F9',
                        charcoal: {
                            DEFAULT: '#18181B',
                            soft: '#27272A',
                        },
                        pastel: {
                            lavender: '#E5D9FA',
                            'lavender-deep': '#D5C2F7',
                            mint: '#D5EDE8',
                            'mint-deep': '#B4DCD3',
                            periwinkle: '#E6E8FA',
                            'periwinkle-deep': '#C7CCF4',
                            butter: '#FBE8A6',
                            'butter-deep': '#F5D061',
                            coral: '#F8C8C4',
                        },
                    },
                    borderRadius: {
                        card: '28px',
                        inner: '22px',
                        dock: '20px',
                        squircle: '14px',
                    }
                }
            }
        };
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #C7D6DD;
            -webkit-tap-highlight-color: transparent;
        }
        .bg-lavender-grid {
            background-color: #E5D9FA;
            background-image:
                linear-gradient(45deg, rgba(196, 173, 240, 0.38) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(196, 173, 240, 0.38) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(196, 173, 240, 0.38) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(196, 173, 240, 0.38) 75%);
            background-size: 64px 64px;
            background-position: 0 0, 0 32px, 32px -32px, -32px 0;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen text-[#111827] antialiased selection:bg-[#E5D9FA] selection:text-[#18181B]">

    <!-- Top Participant Status Bar (Single Role: Peserta Magang) -->
    <div class="max-w-[440px] mx-auto pt-2 px-3">
        <div class="bg-[#18181B] text-white rounded-full px-3.5 py-1.5 flex items-center justify-between text-[11px] shadow-md">
            <div class="flex items-center gap-1.5 min-w-0">
                <span id="network-dot" class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                <span id="network-label" class="font-semibold tracking-tight truncate">
                    @auth
                        {{ auth()->user()->name }} • {{ auth()->user()->activeInternship?->division ?? 'Peserta Magang' }}
                    @else
                        Timesheet MagangHub PWA
                    @endauth
                </span>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('register') }}"
                   class="px-2.5 py-0.5 rounded-full bg-[#E5D9FA] text-[#18181B] font-bold hover:opacity-90 transition">
                    + Daftar Peserta
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile App Shell (Matches style-design.png iPhone Canvas #F6F7F9) -->
    <div class="max-w-[440px] mx-auto min-h-[calc(100vh-3rem)] my-2 bg-[#F6F7F9] sm:rounded-[44px] shadow-2xl border-[6px] border-white relative pb-28 overflow-x-hidden">

        @if (session('toast'))
            <div id="toast-banner" class="mx-5 mt-4 bg-[#18181B] text-white px-4 py-3 rounded-[20px] text-xs font-medium flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-[#D5EDE8] text-[#18181B] flex items-center justify-center font-bold">✓</span>
                    <span>{{ session('toast') }}</span>
                </div>
                <button onclick="document.getElementById('toast-banner').remove()" class="text-zinc-400 hover:text-white ml-2">✕</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-5 mt-4 bg-[#F8C8C4] text-[#18181B] px-4 py-3 rounded-[20px] text-xs font-medium">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main>
            @yield('content')
        </main>

        <!-- Signature Floating Neo-Dock + Detached Squircle FAB (from style-design.png) -->
        <nav aria-label="Menu Utama Peserta" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-40 flex items-center gap-2.5 pointer-events-none">
            <!-- Left 4-Icon Pill Container -->
            <div class="pointer-events-auto flex items-center gap-1.5 bg-white border-[3px] border-[#18181B] rounded-[20px] p-1.5 shadow-[0_12px_28px_rgba(24,24,27,0.16)]">
                <!-- 1. Home / Beranda -->
                <a href="{{ route('dashboard') }}"
                   title="Beranda"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('dashboard') ? 'bg-[#F1F2F6] text-[#18181B]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3.2 3 10.4V20a1 1 0 0 0 1 1h5v-6h6v6h5a1 1 0 0 0 1-1v-9.6L12 3.2Z"/>
                    </svg>
                </a>

                <!-- 2. Calendar / Logbook -->
                <a href="{{ route('logbook.index') }}"
                   title="Kalender & Logbook"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('logbook.index') || request()->routeIs('logbook.show') ? 'bg-[#F1F2F6] text-[#18181B]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect x="3" y="4" width="18" height="17" rx="3"/>
                        <path d="M8 2v4M16 2v4M3 9h18"/>
                        <circle cx="8.5" cy="13.5" r="1" fill="currentColor"/>
                        <circle cx="12" cy="13.5" r="1" fill="currentColor"/>
                        <circle cx="15.5" cy="13.5" r="1" fill="currentColor"/>
                    </svg>
                </a>

                <!-- 3. Reports & PDF -->
                <a href="{{ route('reports.index') }}"
                   title="Laporan PDF"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('reports.*') ? 'bg-[#F1F2F6] text-[#18181B]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect x="3" y="3" width="18" height="18" rx="4"/>
                        <path d="M7 14l3-3 3 2 4-4"/>
                    </svg>
                </a>

                <!-- 4. Profile & Data Peserta -->
                <a href="{{ route('profile.show') }}"
                   title="Data Peserta"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('profile.*') || request()->routeIs('register') ? 'bg-[#F1F2F6] text-[#18181B]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M5 20c0-3.8 3.1-6 7-6s7 2.2 7 6H5Z"/>
                    </svg>
                </a>
            </div>

            <!-- Right Detached Squircle FAB [⊕] -->
            <a href="{{ route('logbook.create') }}"
               title="Tambah Logbook Harian"
               class="pointer-events-auto w-[54px] h-[54px] bg-white border-[3px] border-[#18181B] rounded-[20px] flex items-center justify-center text-[#18181B] shadow-[0_12px_28px_rgba(24,24,27,0.16)] hover:bg-[#E5D9FA] active:scale-95 transition">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v8M8 12h8"/>
                </svg>
            </a>
        </nav>
    </div>

    <!-- Global Modal: Fitur Lanjutan (AI Gemini & Voice-to-Log Preview Alert) -->
    <div id="advanced-feature-modal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="adv-modal-title"
         class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#18181B]/60 backdrop-blur-xs transition-opacity">
        <div class="w-full max-w-[390px] bg-white rounded-[32px] p-5 shadow-2xl border-[3px] border-[#18181B] space-y-4 animate-[fadeIn_0.2s_ease-out]">

            <!-- Modal Top Pastel Lavender Grid Card -->
            <div class="bg-lavender-grid rounded-[24px] p-5 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span id="adv-modal-badge" class="inline-flex items-center gap-1 bg-white px-3 py-1 rounded-full text-[11px] font-bold text-[#18181B] shadow-2xs">
                        ✨ Fitur Lanjutan • Segera Hadir
                    </span>
                    <button type="button"
                            onclick="closeAdvancedFeatureModal()"
                            class="w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center text-[#18181B] font-bold text-xs transition">
                        ✕
                    </button>
                </div>

                <div class="mt-4 flex items-center gap-3.5">
                    <div id="adv-modal-icon" class="w-13 h-13 rounded-[18px] bg-[#18181B] text-[#FBE8A6] flex items-center justify-center text-2xl shrink-0 shadow-md">
                        ✨
                    </div>
                    <div>
                        <h3 id="adv-modal-title" class="text-[18px] font-bold text-[#111827] leading-snug">
                            Asisten AI & Voice-to-Log
                        </h3>
                        <p id="adv-modal-subtitle" class="text-xs font-medium text-zinc-700 mt-0.5">
                            Modul Pintar Timesheet MagangHub
                        </p>
                    </div>
                </div>
            </div>

            <!-- Alert Message Body -->
            <div class="px-1 space-y-3">
                <div class="bg-[#FBE8A6]/70 rounded-[18px] px-3.5 py-2.5 flex items-start gap-2.5">
                    <span class="text-sm mt-0.5">🚀</span>
                    <p id="adv-modal-desc" class="text-xs text-[#111827] font-medium leading-relaxed">
                        Fitur ini merupakan <strong>Fitur Lanjutan (Tahap Pengembangan Fase Berikutnya)</strong> dan sedang dalam proses integrasi akhir ke layanan cloud AI.
                    </p>
                </div>

                <!-- Dark Charcoal Preview Island -->
                <div class="bg-[#18181B] text-white rounded-[22px] p-4 space-y-2">
                    <p class="text-[11px] font-bold text-[#E5D9FA] uppercase tracking-wider">
                        Yang Akan Hadir di Pembaruan Ini:
                    </p>
                    <ul id="adv-modal-list" class="text-xs text-zinc-300 space-y-1.5">
                        <li class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#D5EDE8] text-[#18181B] flex items-center justify-center text-[10px] font-bold shrink-0">✓</span>
                            <span>Perapian kalimat otomatis 3 gaya (Formal, Singkat, Detail)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#FBE8A6] text-[#18181B] flex items-center justify-center text-[10px] font-bold shrink-0">✓</span>
                            <span>Dikte suara (Voice-to-Log) Bahasa Indonesia real-time</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#E5D9FA] text-[#18181B] flex items-center justify-center text-[10px] font-bold shrink-0">✓</span>
                            <span>Saran deskripsi otomatis dari foto dokumentasi</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Action Button -->
            <div class="pt-1">
                <button type="button"
                        onclick="closeAdvancedFeatureModal()"
                        class="w-full bg-[#18181B] hover:bg-zinc-800 text-white text-xs font-bold py-3.5 rounded-full shadow-md transition active:scale-[0.99]">
                    Mengerti, Lanjutkan Isi Manual →
                </button>
            </div>
        </div>
    </div>

    <script>
        const ADVANCED_FEATURE_CONFIG = {
            ai: {
                icon: '✨',
                badge: '✨ Fitur Lanjutan • AI Gemini',
                title: 'Auto-Polish Deskripsi AI',
                subtitle: 'Perapian Bahasa Laporan Otomatis',
                desc: 'Integrasi <strong>Google Gemini AI (Rapikan Teks)</strong> merupakan <strong>Fitur Lanjutan</strong> yang akan diaktifkan pada rilis tahap berikutnya. Saat ini silakan tulis deskripsi kegiatan secara manual atau gunakan <strong>Template Kegiatan Cepat 1-Tap</strong>.'
            },
            voice: {
                icon: '🎙️',
                badge: '🎤 Fitur Lanjutan • Voice-to-Log',
                title: 'Dikte Suara ke Logbook',
                subtitle: 'Transkripsi Suara Otomatis Berbasis AI',
                desc: 'Fitur <strong>Rekam Suara (Voice-to-Log)</strong> merupakan <strong>Fitur Lanjutan</strong> yang sedang disiapkan agar kamu cukup berbicara lewat mikrofon dan teksnya langsung tertulis rapi. Untuk saat ini silakan ketik kegiatanmu di kolom deskripsi.'
            },
            photo: {
                icon: '📷',
                badge: '📷 Fitur Lanjutan • AI Vision',
                title: 'Saran Deskripsi dari Foto',
                subtitle: 'Deteksi Aktivitas Dokumentasi Otomatis',
                desc: 'Fitur <strong>Saran Deskripsi dari Foto</strong> merupakan <strong>Fitur Lanjutan</strong> berbasis AI Multimodal. Kamu tetap bisa mengunggah foto bukti kegiatan secara normal pada form di bawah.'
            },
            summary: {
                icon: '📊',
                badge: '✨ Fitur Lanjutan • AI Summary',
                title: 'Ringkasan Eksekutif & Skill AI',
                subtitle: 'Rekap Narasi Bulanan Otomatis',
                desc: 'Fitur <strong>Ringkasan Narasi AI & Ekstraksi Kompetensi</strong> merupakan <strong>Fitur Lanjutan</strong>. Saat ini pencetakan tabel kegiatan dan ekspor laporan PDF A4 sudah berfungsi penuh dan siap diunduh.'
            }
        };

        function openAdvancedFeatureModal(type = 'ai') {
            const modal = document.getElementById('advanced-feature-modal');
            const cfg = ADVANCED_FEATURE_CONFIG[type] || ADVANCED_FEATURE_CONFIG.ai;

            document.getElementById('adv-modal-icon').textContent = cfg.icon;
            document.getElementById('adv-modal-badge').textContent = cfg.badge;
            document.getElementById('adv-modal-title').textContent = cfg.title;
            document.getElementById('adv-modal-subtitle').textContent = cfg.subtitle;
            document.getElementById('adv-modal-desc').innerHTML = cfg.desc;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAdvancedFeatureModal() {
            const modal = document.getElementById('advanced-feature-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAdvancedFeatureModal();
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }

        function updateNetworkIndicator() {
            const dot = document.getElementById('network-dot');
            if (!dot) return;
            if (navigator.onLine) {
                dot.className = 'w-2 h-2 rounded-full bg-emerald-400 shrink-0';
                syncOfflineQueue();
            } else {
                dot.className = 'w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0';
            }
        }

        async function syncOfflineQueue() {
            const raw = localStorage.getItem('maganghub_offline_logs');
            if (!raw) return;
            try {
                const items = JSON.parse(raw);
                if (!Array.isArray(items) || items.length === 0) return;
                const res = await fetch('/api/logbooks/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ items }),
                });
                if (res.ok) {
                    localStorage.removeItem('maganghub_offline_logs');
                }
            } catch (e) {}
        }

        window.addEventListener('online', updateNetworkIndicator);
        window.addEventListener('offline', updateNetworkIndicator);
        document.addEventListener('DOMContentLoaded', () => {
            updateNetworkIndicator();
            const params = new URLSearchParams(window.location.search);
            if (params.get('voice') === '1') {
                openAdvancedFeatureModal('voice');
            }
        });
    </script>
</body>
</html>
