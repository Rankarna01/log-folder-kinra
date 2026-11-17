@props(['todayLog' => null])

<!-- Mobile only: Signature Floating Neo-Dock + Conditional Create FAB (hidden when today's logbook already exists) -->
<nav aria-label="Menu Utama Peserta" class="lg:hidden fixed bottom-5 left-1/2 -translate-x-1/2 z-40 flex items-center gap-2.5 pointer-events-none">
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
    @if (! $todayLog)
        <a href="{{ route('logbook.create') }}"
           title="Tambah Logbook Hari Ini"
           class="pointer-events-auto w-[54px] h-[54px] bg-white border-[3px] border-[#18181B] rounded-[20px] flex items-center justify-center text-[#18181B] shadow-[0_12px_28px_rgba(24,24,27,0.16)] hover:bg-[#0B6E6C] hover:text-white active:scale-95 transition">
            <i class="fa-solid fa-circle-plus text-[22px]"></i>
        </a>
    @endif
</nav>
