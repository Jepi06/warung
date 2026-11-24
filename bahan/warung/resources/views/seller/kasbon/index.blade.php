@extends('layouts.seller')
@section('title', 'Kasbon')

@section('content')
<div class="flex flex-col gap-5">
  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
      <h1 class="text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Buku Kasbon</h1>
      <p class="text-body-md text-on-surface-variant">Catatan pelanggan yang belanja dulu, bayar belakangan.</p>
    </div>
    <a href="{{ route('seller.kasbon.create') }}" class="btn-primary">
      <span class="material-symbols-outlined text-[20px]">add</span> Catat kasbon
    </a>
  </div>

  <div class="grid gap-3 sm:grid-cols-2">
    <div class="panel flex items-center gap-4 p-5">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-secondary-fixed text-secondary"><span class="material-symbols-outlined text-[26px]">account_balance_wallet</span></div>
      <div>
        <div class="text-label-md font-bold uppercase tracking-wider text-on-surface-variant">Total belum dibayar</div>
        <div class="text-currency-stat">Rp {{ number_format($total, 0, ',', '.') }}</div>
      </div>
    </div>
    <div class="panel flex items-center gap-4 p-5">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-fixed/50 text-primary"><span class="material-symbols-outlined text-[26px]">groups</span></div>
      <div>
        <div class="text-label-md font-bold uppercase tracking-wider text-on-surface-variant">Pelanggan berutang</div>
        <div class="text-currency-stat">{{ $openCount }}</div>
      </div>
    </div>
  </div>

  <div x-data="{ q: '' }" class="flex flex-col gap-3">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex gap-1.5">
        <a href="{{ route('seller.kasbon.index') }}" class="rounded-full px-4 py-1.5 text-label-md {{ $filter === 'open' ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-low hover:bg-surface-container-high' }}">Belum lunas</a>
        <a href="{{ route('seller.kasbon.index', ['show' => 'all']) }}" class="rounded-full px-4 py-1.5 text-label-md {{ $filter === 'all' ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-low hover:bg-surface-container-high' }}">Semua</a>
      </div>
      <div class="relative w-full sm:w-64">
        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-outline">search</span>
        <input type="search" x-model="q" placeholder="Cari nama…" class="input !pl-9">
      </div>
    </div>

    <div class="panel divide-y divide-outline-variant/30 overflow-hidden">
      @forelse ($debtors as $d)
        <div data-name="{{ mb_strtolower($d->name) }}" x-show="$el.dataset.name.includes(q.toLowerCase())"
             class="flex items-center justify-between gap-3 p-4 transition-colors hover:bg-surface-container-low">
          <a href="{{ route('seller.kasbon.show', $d) }}" class="flex min-w-0 flex-1 items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-tertiary-fixed font-bold text-tertiary">{{ mb_strtoupper(mb_substr($d->name, 0, 2)) }}</div>
            <div class="min-w-0">
              <div class="truncate text-label-lg font-bold">{{ $d->name }}</div>
              <div class="text-label-sm text-on-surface-variant">
                @if ($d->last_date) Terakhir {{ \Carbon\Carbon::parse($d->last_date)->locale('id')->translatedFormat('d M Y') }} @else Belum ada catatan @endif
              </div>
            </div>
          </a>
          <div class="flex shrink-0 items-center gap-2">
            @if ($d->balance > 0)
              <span class="text-headline-sm font-extrabold text-error">Rp {{ number_format($d->balance, 0, ',', '.') }}</span>
              @if ($d->waReminderUrl())
                <a href="{{ $d->waReminderUrl() }}" target="_blank" class="btn-ghost !px-3 !py-1.5" title="Kirim pengingat lewat WhatsApp">
                  <span class="material-symbols-outlined text-[18px]">send</span><span class="hidden sm:inline">Tagih</span>
                </a>
              @endif
            @else
              <span class="rounded-full bg-primary-fixed px-2.5 py-0.5 text-label-sm font-bold text-on-primary-fixed">Lunas</span>
            @endif
          </div>
        </div>
      @empty
        <p class="px-4 py-10 text-center text-body-md text-on-surface-variant">
          {{ $filter === 'open' ? 'Tidak ada kasbon yang belum dibayar.' : 'Belum ada catatan kasbon.' }}
        </p>
      @endforelse
    </div>
  </div>
</div>
@endsection
