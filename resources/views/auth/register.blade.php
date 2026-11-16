@extends('layouts.app')

@section('content')
<div class="px-5 pt-6 pb-8 space-y-5">

    <!-- Hero KINRA Deep Green Grid Onboarding Card + Official Logo -->
    <section class="bg-kinra-grid rounded-[28px] p-6 shadow-xs relative overflow-hidden text-white">
        <div class="flex items-center justify-between gap-2">
            <div class="bg-white rounded-2xl px-3.5 py-2 shadow-xs inline-flex items-center">
                <img src="/images/kinra.png" alt="Logo KINRA Sei Mangkei" class="h-7 w-auto object-contain">
            </div>
            <span class="inline-block bg-white/95 px-3 py-1 rounded-full text-[11px] font-bold text-[#0B6E6C] shadow-2xs">
                ✨ Registrasi Peserta Magang
            </span>
        </div>

        <h1 class="text-[22px] font-bold text-white mt-4 leading-snug">
            Mulai Catat Logbook Harianmu
        </h1>
        <p class="text-xs text-emerald-100 mt-1 leading-relaxed">
            Isi data singkat di bawah ini. Setelah daftar, kamu akan otomatis diarahkan ke form tambah kegiatan harian.
        </p>
    </section>

    <!-- Simple 4-Field Participant Registration Form -->
    <form action="{{ route('register.submit') }}" method="POST" class="bg-white rounded-[28px] p-5 space-y-4 shadow-2xs">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-[#111827]">1. Nama Lengkap Peserta</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   placeholder="Contoh: Randy Karna / Budi Santoso"
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <div>
            <label for="division" class="block text-xs font-semibold text-[#111827]">2. Divisi</label>
            <input type="text" id="division" name="division" value="{{ old('division') }}" required
                   placeholder="Contoh: IT & Digital Product / Operasional / SDM"
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <div>
            <label for="position" class="block text-xs font-semibold text-[#111827]">3. Bagian / Posisi Magang</label>
            <input type="text" id="position" name="position" value="{{ old('position') }}" required
                   placeholder="Contoh: Full Stack Developer / Administrasi"
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <div>
            <label for="mentor_name" class="block text-xs font-semibold text-[#111827]">4. Nama Mentor Pembimbing</label>
            <input type="text" id="mentor_name" name="mentor_name" value="{{ old('mentor_name') }}" required
                   placeholder="Contoh: Rizky / Agnes Nielsen"
                   class="mt-1.5 w-full rounded-[16px] bg-[#F4F7F6] border-0 px-4 py-3 text-sm font-medium text-[#111827] focus:ring-2 focus:ring-[#0B6E6C]">
        </div>

        <button type="submit"
                class="w-full bg-[#0B6E6C] hover:bg-[#085856] text-white text-sm font-bold py-3.5 rounded-full shadow-md transition active:scale-[0.99]">
            Simpan & Tambah Logbook Harian →
        </button>
    </form>

    <!-- Existing / Demo Participants Quick Resume -->
    @if (isset($recentParticipants) && $recentParticipants->isNotEmpty())
        <section class="bg-[#C8EBE5] rounded-[26px] p-4 space-y-2.5">
            <p class="text-xs font-bold text-[#074745]">Lanjutkan Data Peserta yang Sudah Terdaftar:</p>
            <div class="space-y-2">
                @foreach ($recentParticipants as $p)
                    <form action="{{ route('participant.quick-login', $p) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full bg-white hover:bg-[#FDE9B4] rounded-[18px] p-3 flex items-center justify-between text-left transition shadow-2xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-9 h-9 rounded-full bg-[#0B6E6C] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ $p->initials }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#111827] truncate">{{ $p->name }}</p>
                                    <p class="text-[11px] text-zinc-500 truncate">
                                        {{ $p->activeInternship?->division ?? $p->campus }} • {{ $p->activeInternship?->position ?? $p->major }} • Mentor: {{ $p->activeInternship?->mentor?->name ?? ($p->preferences['mentor_name'] ?? '-') }}
                                    </p>
                                </div>
                            </div>
                            <span class="bg-[#0B6E6C] text-white text-[11px] font-semibold px-3 py-1 rounded-full shrink-0">
                                Masuk →
                            </span>
                        </button>
                    </form>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
