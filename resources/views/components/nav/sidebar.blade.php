@props(['todayLog' => null])

@php
    $menuItems = [
        ['label' => 'Beranda', 'icon' => 'fa-house', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ['label' => 'Kalender & Riwayat', 'icon' => 'fa-calendar-days', 'href' => route('logbook.index'), 'active' => request()->routeIs('logbook.index') || request()->routeIs('logbook.show')],
        ['label' => 'Laporan PDF', 'icon' => 'fa-file-pdf', 'href' => route('reports.index'), 'active' => request()->routeIs('reports.*')],
        ['label' => 'Data Peserta', 'icon' => 'fa-user', 'href' => route('profile.show'), 'active' => request()->routeIs('profile.*') || request()->routeIs('register')],
    ];
@endphp

<!-- Desktop only (laptop screens): fixed sidebar navigation -->
<aside aria-label="Menu Utama Peserta"
       class="hidden lg:flex fixed inset-y-0 left-0 z-40 w-[270px] flex-col gap-8 border-r border-[#D5E8E5] bg-white/75 px-5 py-7 backdrop-blur-md">
    <div class="px-2">
        <img src="/images/kinra.png" alt="KINRA" class="h-16 w-auto object-contain object-left">
    </div>

    <nav class="flex flex-col gap-1.5">
        @foreach ($menuItems as $item)
            <a href="{{ $item['href'] }}"
               class="flex items-center gap-3.5 rounded-[16px] px-4 py-3 text-[15px] transition {{ $item['active'] ? 'bg-[#C8EBE5] font-semibold text-[#0B6E6C]' : 'font-medium text-[#334155] hover:bg-white' }}">
                <i class="fa-solid {{ $item['icon'] }} w-5 text-center text-[16px]"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

</aside>
