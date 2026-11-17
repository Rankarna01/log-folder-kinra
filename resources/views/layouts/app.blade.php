<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B6E6C">
    <link rel="icon" type="image/png" href="/images/kinra.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>{{ $title ?? 'Timesheet KINRA Sei Mangkei — Logbook Harian Peserta' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                        canvas: '#F4F7F6',
                        charcoal: {
                            DEFAULT: '#18181B',
                            soft: '#27272A',
                        },
                        kinra: {
                            DEFAULT: '#0B6E6C',
                            dark: '#075250',
                            light: '#C8EBE5',
                            sky: '#D4EEFC',
                            amber: '#FDE9B4',
                            terracotta: '#FCE1D4',
                        },
                        pastel: {
                            lavender: '#C8EBE5',
                            'lavender-deep': '#0B6E6C',
                            mint: '#C8EBE5',
                            'mint-deep': '#0D7A78',
                            periwinkle: '#D4EEFC',
                            'periwinkle-deep': '#209CE2',
                            butter: '#FDE9B4',
                            'butter-deep': '#F7A61C',
                            coral: '#B8E4DD',
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
            background-color: #BFD4D2;
            -webkit-tap-highlight-color: transparent;
        }
        .bg-lavender-grid,
        .bg-kinra-grid {
            background-color: #0B6E6C;
            background-image:
                linear-gradient(45deg, rgba(6, 78, 76, 0.55) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(6, 78, 76, 0.55) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(6, 78, 76, 0.55) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(6, 78, 76, 0.55) 75%);
            background-size: 64px 64px;
            background-position: 0 0, 0 32px, 32px -32px, -32px 0;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen text-[#111827] antialiased selection:bg-[#0B6E6C] selection:text-white">

    @php
        // Auto-purge any legacy demo user (Esther Howard / Agnes Nielsen) on every view render
        if (auth()->check() && (in_array(auth()->user()->name, ['Esther Howard', 'Agnes Nielsen'], true) || in_array(auth()->user()->email, ['peserta@maganghub.id', 'mentor@maganghub.id', 'admin@maganghub.id'], true))) {
            $oldDemo = auth()->user();
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            \App\Models\Internship::where('participant_id', $oldDemo->id)->delete();
            $oldDemo->forceDelete();
        }
        $navInternship = auth()->user()?->activeInternship;
        $navTodayLog = $navInternship
            ? $navInternship->logbooks()->whereDate('date', \Carbon\Carbon::now('Asia/Jakarta')->toDateString())->first()
            : null;
        $isAuthPage = request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('onboarding.*');
    @endphp

    <!-- Top Participant Status Bar with KINRA Logo & Account Controls -->
    <div class="max-w-[440px] mx-auto pt-2 px-3">
        <div class="bg-[#18181B] text-white rounded-full px-3 py-1.5 flex items-center justify-between text-[11px] shadow-md">
            <div class="flex items-center gap-2 min-w-0">
                <span class="bg-white rounded-full px-2 py-0.5 flex items-center justify-center shrink-0">
                    <img src="/images/kinra.png" alt="KINRA" class="h-3.5 w-auto object-contain">
                </span>
                <span id="network-dot" class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                <span id="network-label" class="font-semibold tracking-tight truncate">
                    @auth
                        {{ auth()->user()->name }} • {{ auth()->user()->activeInternship?->division ?? auth()->user()->email }}
                    @else
                        Timesheet KINRA Sei Mangkei
                    @endauth
                </span>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-3 py-0.5 rounded-full bg-[#0B6E6C] text-white border border-emerald-400/30 font-bold hover:bg-[#0D8280] transition flex items-center gap-1">
                            <i class="fa-solid fa-right-from-bracket text-[10px]"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="px-3 py-0.5 rounded-full bg-[#0B6E6C] text-white border border-emerald-400/30 font-bold hover:bg-[#0D8280] transition flex items-center gap-1">
                        <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                        <span>Masuk</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile App Shell -->
    <div class="max-w-[440px] mx-auto min-h-[calc(100vh-3rem)] my-2 bg-[#F4F7F6] sm:rounded-[44px] shadow-2xl border-[6px] border-white relative pb-28 overflow-x-hidden">

        @if (session('toast'))
            <div id="toast-banner" class="mx-5 mt-4 bg-[#0B6E6C] text-white px-4 py-3 rounded-[20px] text-xs font-medium flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-[#FDE9B4] text-[#18181B] flex items-center justify-center font-bold">
                        <i class="fa-solid fa-check text-[10px]"></i>
                    </span>
                    <span>{{ session('toast') }}</span>
                </div>
                <button onclick="document.getElementById('toast-banner').remove()" class="text-emerald-100 hover:text-white ml-2">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-5 mt-4 bg-[#FCE1D4] text-[#18181B] px-4 py-3 rounded-[20px] text-xs font-medium">
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

        @if (! $isAuthPage)
        <!-- Signature Floating Neo-Dock + Conditional Create FAB (Hidden when today's logbook already exists) -->
        <nav aria-label="Menu Utama Peserta" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-40 flex items-center gap-2.5 pointer-events-none">
            <!-- Left 4-Icon Pill Container (Font Awesome Icons) -->
            <div class="pointer-events-auto flex items-center gap-1.5 bg-white border-[3px] border-[#18181B] rounded-[20px] p-1.5 shadow-[0_12px_28px_rgba(24,24,27,0.16)]">
                <!-- 1. Home / Beranda -->
                <a href="{{ route('dashboard') }}"
                   title="Beranda"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('dashboard') ? 'bg-[#C8EBE5] text-[#0B6E6C]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <i class="fa-solid fa-house text-[16px]"></i>
                </a>

                <!-- 2. Calendar / Logbook -->
                <a href="{{ route('logbook.index') }}"
                   title="Kalender & Riwayat Logbook"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('logbook.index') || request()->routeIs('logbook.show') ? 'bg-[#C8EBE5] text-[#0B6E6C]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <i class="fa-solid fa-calendar-days text-[16px]"></i>
                </a>

                <!-- 3. Reports & PDF -->
                <a href="{{ route('reports.index') }}"
                   title="Laporan PDF"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('reports.*') ? 'bg-[#C8EBE5] text-[#0B6E6C]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <i class="fa-solid fa-file-pdf text-[16px]"></i>
                </a>

                <!-- 4. Profile & Data Peserta -->
                <a href="{{ route('profile.show') }}"
                   title="Data Peserta"
                   class="w-11 h-10 rounded-[13px] flex items-center justify-center transition {{ request()->routeIs('profile.*') || request()->routeIs('register') ? 'bg-[#C8EBE5] text-[#0B6E6C]' : 'text-zinc-500 hover:bg-zinc-100' }}">
                    <i class="fa-solid fa-user text-[16px]"></i>
                </a>
            </div>

            <!-- Right Detached Squircle FAB: Only shown if today's logbook has NOT been created yet -->
            @if (! $navTodayLog)
                <a href="{{ route('logbook.create') }}"
                   title="Tambah Logbook Hari Ini"
                   class="pointer-events-auto w-[54px] h-[54px] bg-white border-[3px] border-[#18181B] rounded-[20px] flex items-center justify-center text-[#18181B] shadow-[0_12px_28px_rgba(24,24,27,0.16)] hover:bg-[#0B6E6C] hover:text-white active:scale-95 transition">
                    <i class="fa-solid fa-circle-plus text-[22px]"></i>
                </a>
            @endif
        </nav>
        @endif
    </div>

    <!-- Global Modal: Fitur Lanjutan (Single Checkerboard Grid Card Only) -->
    <div id="advanced-feature-modal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="adv-modal-title"
         onclick="if (event.target === this) closeAdvancedFeatureModal()"
         class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#18181B]/60 backdrop-blur-xs transition-opacity">
        <div class="w-full max-w-[370px] bg-kinra-grid rounded-[28px] p-5 shadow-2xl border-[3px] border-[#18181B] text-white space-y-4">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 bg-white px-3 py-1 rounded-full text-[11px] font-bold text-[#0B6E6C] shadow-2xs">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span id="adv-modal-badge">Fitur Lanjutan • Segera Hadir</span>
                </span>
                <button type="button"
                        onclick="closeAdvancedFeatureModal()"
                        class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white font-bold text-xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex items-center gap-3.5 pt-1">
                <div class="w-13 h-13 rounded-[18px] bg-[#18181B] text-[#FDE9B4] flex items-center justify-center text-xl shrink-0 shadow-md">
                    <i id="adv-modal-icon" class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h3 id="adv-modal-title" class="text-[18px] font-bold text-white leading-snug">
                        Asisten AI & Voice-to-Log
                    </h3>
                    <p id="adv-modal-subtitle" class="text-xs font-medium text-emerald-100 mt-0.5">
                        Modul Pintar Timesheet KINRA Sei Mangkei
                    </p>
                </div>
            </div>

            <button type="button"
                    onclick="closeAdvancedFeatureModal()"
                    class="w-full bg-[#18181B] hover:bg-zinc-800 text-white text-xs font-bold py-3 rounded-full shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Mengerti</span>
                <i class="fa-solid fa-check"></i>
            </button>
        </div>
    </div>

    <script>
        const ADVANCED_FEATURE_CONFIG = {
            ai: {
                iconClass: 'fa-solid fa-wand-magic-sparkles',
                badge: 'Fitur Lanjutan • AI Gemini',
                title: 'Auto-Polish Deskripsi AI',
                subtitle: 'Perapian Bahasa Laporan Otomatis',
                desc: 'Integrasi <strong>Google Gemini AI (Rapikan Teks)</strong> merupakan <strong>Fitur Lanjutan</strong> yang akan diaktifkan pada rilis tahap berikutnya. Saat ini silakan tulis deskripsi kegiatan secara manual atau gunakan <strong>Template Kegiatan Cepat 1-Tap</strong>.'
            },
            voice: {
                iconClass: 'fa-solid fa-microphone-lines',
                badge: 'Fitur Lanjutan • Voice-to-Log',
                title: 'Dikte Suara ke Logbook',
                subtitle: 'Transkripsi Suara Otomatis Berbasis AI',
                desc: 'Fitur <strong>Rekam Suara (Voice-to-Log)</strong> merupakan <strong>Fitur Lanjutan</strong> yang sedang disiapkan agar kamu cukup berbicara lewat mikrofon dan teksnya langsung tertulis rapi. Untuk saat ini silakan ketik kegiatanmu di kolom deskripsi.'
            },
            photo: {
                iconClass: 'fa-solid fa-camera-retro',
                badge: 'Fitur Lanjutan • AI Vision',
                title: 'Saran Deskripsi dari Foto',
                subtitle: 'Deteksi Aktivitas Dokumentasi Otomatis',
                desc: 'Fitur <strong>Saran Deskripsi dari Foto</strong> merupakan <strong>Fitur Lanjutan</strong> berbasis AI Multimodal. Kamu tetap bisa mengunggah foto bukti kegiatan secara normal pada form di bawah.'
            },
            summary: {
                iconClass: 'fa-solid fa-chart-pie',
                badge: 'Fitur Lanjutan • AI Summary',
                title: 'Ringkasan Eksekutif & Skill AI',
                subtitle: 'Rekap Narasi Bulanan Otomatis',
                desc: 'Fitur <strong>Ringkasan Narasi AI & Ekstraksi Kompetensi</strong> merupakan <strong>Fitur Lanjutan</strong>. Saat ini pencetakan tabel kegiatan dan ekspor laporan PDF A4 sudah berfungsi penuh dan siap diunduh.'
            },
            template: {
                iconClass: 'fa-solid fa-layer-group',
                badge: 'Fitur Lanjutan • Smart Template',
                title: 'Template Catatan Harian Pintar',
                subtitle: 'Pengisian Cepat Kegiatan Berulang',
                desc: 'Fitur <strong>Tambah & Gunakan Template Catatan Harian</strong> merupakan <strong>Fitur Lanjutan</strong> yang sedang disiapkan untuk pembaruan berikutnya. Saat ini silakan isi kegiatan harianmu langsung pada form utama.'
            }
        };

        function openAdvancedFeatureModal(type = 'ai') {
            const modal = document.getElementById('advanced-feature-modal');
            const cfg = ADVANCED_FEATURE_CONFIG[type] || ADVANCED_FEATURE_CONFIG.ai;

            document.getElementById('adv-modal-icon').className = cfg.iconClass;
            document.getElementById('adv-modal-badge').textContent = cfg.badge;
            document.getElementById('adv-modal-title').textContent = cfg.title;
            document.getElementById('adv-modal-subtitle').textContent = cfg.subtitle;

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

        if ('caches' in window) {
            caches.keys().then((keys) => {
                keys.forEach((k) => {
                    if (k !== 'kinra-pwa-v3') caches.delete(k);
                });
            });
        }
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((reg) => reg.update()).catch(() => {});
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
