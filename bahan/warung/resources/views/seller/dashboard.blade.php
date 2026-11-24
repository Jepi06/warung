@extends('layouts.seller')
@section('title', 'Ringkasan')

@section('content')
@php
  $rp = fn ($n) => ($n < 0 ? '−' : '') . 'Rp ' . number_format(abs($n), 0, ',', '.');
  $presets = [
    'today' => ['Hari Ini',        now()->toDateString(),                  now()->toDateString()],
    '7d'    => ['7 Hari Terakhir', now()->subDays(6)->toDateString(),      now()->toDateString()],
    'month' => ['Bulan Ini',       now()->startOfMonth()->toDateString(),  now()->toDateString()],
  ];
  $pctText = fn ($t) => ($t >= 0 ? '+' : '−') . number_format(abs($t), 1, ',', '.') . '%';
  $negative = $cur['netProfit'] < 0;
@endphp

<div class="flex flex-col gap-6">

  {{-- Judul & aksi cepat --}}
  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
      <h1 class="text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Ringkasan Bisnis</h1>
      <p class="text-body-md text-on-surface-variant">Pantau penjualan, laba bersih, dan arus kas warung.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('seller.products.create') }}" class="btn-ghost">
        <span class="material-symbols-outlined text-[18px]">add_box</span> Tambah Barang
      </a>
      <a href="{{ route('seller.kasir.index') }}" class="btn-primary !px-5">
        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' 1">point_of_sale</span> Mulai Jual di Kasir
      </a>
    </div>
  </div>

  {{-- Filter tanggal --}}
  <form method="GET" class="panel flex flex-col justify-between gap-3 p-4 xl:flex-row xl:items-center">
    <div class="flex flex-wrap gap-1.5">
      @foreach ($presets as $key => [$label, $pf, $pt])
        <a href="{{ route('seller.dashboard', ['from' => $pf, 'to' => $pt]) }}"
           class="rounded-full px-4 py-1.5 text-label-md transition-colors {{ $preset === $key ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-low text-on-surface hover:bg-surface-container-high' }}">{{ $label }}</a>
      @endforeach
      <button type="button" onclick="document.getElementById('from').focus()"
              class="rounded-full px-4 py-1.5 text-label-md transition-colors {{ $preset === 'custom' ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-low text-on-surface hover:bg-surface-container-high' }}">Kustom</button>
    </div>
    <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:items-center">
      <label class="flex items-center gap-2 rounded-xl bg-surface-container-low px-3 py-1.5">
        <span class="text-label-sm uppercase text-on-surface-variant">Dari</span>
        <input id="from" type="date" name="from" value="{{ $from->toDateString() }}" class="w-full bg-transparent text-label-md focus:outline-none">
      </label>
      <label class="flex items-center gap-2 rounded-xl bg-surface-container-low px-3 py-1.5">
        <span class="text-label-sm uppercase text-on-surface-variant">Sampai</span>
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="w-full bg-transparent text-label-md focus:outline-none">
      </label>
      <button class="btn-primary col-span-2 !py-2 sm:col-span-1">Tampilkan Data</button>
    </div>
  </form>

  {{-- Laba bersih --}}
  <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br p-5 text-on-primary shadow-lg md:p-8 {{ $negative ? 'from-on-error-container via-error to-error' : 'from-primary via-primary-container to-surface-tint' }}">
    <svg class="pointer-events-none absolute -right-8 -top-12 h-96 w-96 text-white/5" fill="currentColor" viewBox="0 0 200 200" aria-hidden="true">
      <path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.6,81.4,29.1,73.1,42.1C64.8,55.1,53.8,66.5,40.4,73.7C27.1,80.9,11.5,83.9,-3.3,89.6C-18.1,95.3,-32.1,103.7,-44.6,99.3C-57.2,94.9,-68.3,77.7,-76.3,61.9C-84.3,46.1,-89.2,31.7,-89.5,17.7C-89.9,3.7,-85.7,-9.9,-80.1,-22.8C-74.5,-35.7,-67.5,-47.9,-56.9,-56.2C-46.3,-64.5,-32.1,-68.9,-17.8,-71.4C-3.6,-73.9,10.6,-74.5,24.7,-75.2C38.8,-75.9,52.8,-76.7,44.7,-76.4Z" transform="translate(100 100)"></path>
    </svg>

    <div class="relative z-10 flex flex-col gap-5">
      <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div class="flex flex-col gap-1.5">
          <div class="flex items-center gap-2 opacity-90">
            <span class="rounded-lg bg-white/10 p-1"><span class="material-symbols-outlined text-[18px]">account_balance_wallet</span></span>
            <span class="text-label-lg font-bold uppercase tracking-wider">Laba Bersih Periode Ini</span>
          </div>
          <div class="flex flex-wrap items-baseline gap-3">
            <span class="text-display-mobile font-extrabold tracking-tight md:text-display">{{ $rp($cur['netProfit']) }}</span>
            @if ($trends['net'] !== null)
              <span class="inline-flex items-center gap-0.5 rounded-full bg-primary-fixed px-3 py-1 text-label-md font-bold text-on-primary-fixed">
                <span class="material-symbols-outlined text-[16px]">{{ $trends['net'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                {{ $pctText($trends['net']) }} vs periode lalu
              </span>
            @endif
          </div>
          <p class="max-w-2xl text-body-md opacity-90">
            Laba penjualan <span class="font-bold">{{ $rp($cur['salesProfit']) }}</span> dikurangi biaya lain di luar belanja stok <span class="font-bold">{{ $rp($cur['otherCost']) }}</span>.
          </p>
        </div>

        <div class="flex gap-2 self-start">
          <div class="flex flex-col rounded-xl bg-white/10 p-3 backdrop-blur-md">
            <span class="text-label-sm uppercase opacity-80">Marjin</span>
            <span class="mt-0.5 text-headline-sm font-bold">{{ $margin !== null ? number_format($margin, 1, ',', '.') . '%' : '–' }}</span>
          </div>
          <div class="flex flex-col rounded-xl bg-white/10 p-3 backdrop-blur-md">
            <span class="text-label-sm uppercase opacity-80">Pesanan Selesai</span>
            <span class="mt-0.5 text-headline-sm font-bold">{{ $cur['orders'] }}</span>
          </div>
        </div>
      </div>

      @if ($cur['noCost'] > 0)
        <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-secondary-fixed px-4 py-3 text-on-secondary-fixed shadow-sm">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[22px] text-secondary">warning</span>
            <span class="text-body-sm"><strong class="font-bold">{{ $cur['noCost'] }} barang terjual</strong> belum punya harga beli, jadi laba bisa terlihat lebih besar dari sebenarnya.</span>
          </div>
          <a href="{{ route('seller.stock.index') }}" class="inline-flex items-center gap-1 text-label-sm font-bold text-secondary hover:underline">
            Isi harga beli <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
          </a>
        </div>
      @endif
    </div>
  </section>

  {{-- Tiga angka utama --}}
  <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
    <div class="panel flex flex-col justify-between gap-3 p-5 transition-shadow hover:shadow-md">
      <div class="flex items-start justify-between gap-2">
        <div>
          <span class="text-label-md font-bold uppercase tracking-wider text-on-surface-variant">Total Pemasukan</span>
          <div class="mt-1 text-currency-stat">{{ $rp($cur['income']) }}</div>
        </div>
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-fixed/60 text-primary"><span class="material-symbols-outlined text-[24px]">payments</span></div>
      </div>
      <div class="flex items-center justify-between text-label-sm">
        <span class="text-on-surface-variant">Uang masuk ke kas</span>
        @if ($trends['income'] !== null)
          <span class="font-bold {{ $trends['income'] >= 0 ? 'text-primary' : 'text-error' }}">{{ $pctText($trends['income']) }}</span>
        @endif
      </div>
    </div>

    <div class="panel flex flex-col justify-between gap-3 p-5 transition-shadow hover:shadow-md">
      <div class="flex items-start justify-between gap-2">
        <div>
          <span class="text-label-md font-bold uppercase tracking-wider text-on-surface-variant">Total Pengeluaran</span>
          <div class="mt-1 text-currency-stat text-error">{{ $rp($cur['expense']) }}</div>
        </div>
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-error-container text-error"><span class="material-symbols-outlined text-[24px]">shopping_cart_checkout</span></div>
      </div>
      <div class="flex flex-col gap-0.5 text-label-sm text-on-surface-variant">
        <div class="flex justify-between"><span>Belanja stok: <strong>{{ $rp($cur['stockCost']) }}</strong></span>
          @if ($trends['expense'] !== null)<span class="font-bold">{{ $pctText($trends['expense']) }}</span>@endif
        </div>
        <span>Biaya lain: <strong>{{ $rp($cur['otherCost']) }}</strong></span>
      </div>
    </div>

    <div class="panel flex flex-col justify-between gap-3 p-5 transition-shadow hover:shadow-md">
      @php $net = $cur['income'] - $cur['expense']; @endphp
      <div class="flex items-start justify-between gap-2">
        <div>
          <span class="text-label-md font-bold uppercase tracking-wider text-on-surface-variant">Selisih Kas</span>
          <div class="mt-1 text-currency-stat {{ $net < 0 ? 'text-error' : 'text-primary' }}">{{ $rp($net) }}</div>
        </div>
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[24px]">savings</span></div>
      </div>
      <div class="flex flex-col">
        <span class="text-label-sm font-bold {{ $net < 0 ? 'text-error' : 'text-primary' }}">{{ $net < 0 ? 'Pengeluaran lebih besar' : 'Pemasukan lebih besar' }}</span>
        <span class="text-body-sm text-on-surface-variant">Pemasukan dikurangi pengeluaran, termasuk belanja stok</span>
      </div>
    </div>
  </div>

  {{-- Pesanan & stok --}}
  <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
    <section class="panel flex flex-col overflow-hidden lg:col-span-7">
      <div class="flex items-center justify-between gap-2 p-5">
        <div class="flex items-center gap-2">
          <h2 class="text-headline-sm font-bold">Pesanan Menunggu</h2>
          <span class="rounded-full bg-secondary-fixed px-2 py-0.5 text-label-sm font-bold text-on-secondary-fixed">{{ $pendingCount }} aktif</span>
        </div>
        <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-0.5 text-label-md font-semibold text-primary hover:underline">
          Lihat semua <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        </a>
      </div>
      <div class="flex flex-col">
        @forelse ($pendingOrders as $o)
          <a href="{{ route('seller.orders.show', $o) }}" class="flex items-center justify-between gap-3 p-4 transition-colors hover:bg-surface-container-low">
            <div class="flex min-w-0 items-center gap-3">
              <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-fixed/40 font-bold text-primary">{{ mb_strtoupper(mb_substr($o->customer_name, 0, 2)) }}</div>
              <div class="flex min-w-0 flex-col">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="truncate text-label-lg font-bold">{{ $o->customer_name }}</span>
                  <span class="font-mono text-label-sm text-on-surface-variant">#{{ $o->id }}</span>
                </div>
                <p class="truncate text-body-sm text-on-surface-variant">{{ $o->items->count() }} item: {{ $o->items->map(fn ($i) => $i->product_name . ' ×' . $i->qty)->join(', ') }}</p>
                <span class="mt-0.5 flex items-center gap-1 text-label-sm text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">schedule</span>{{ $o->created_at->locale('id')->diffForHumans() }}</span>
              </div>
            </div>
            <div class="flex shrink-0 flex-col items-end gap-1">
              <span class="text-headline-sm font-extrabold">Rp {{ number_format($o->total, 0, ',', '.') }}</span>
              <span class="rounded-full bg-secondary-fixed px-2 py-0.5 text-label-sm font-bold text-on-secondary-fixed">Perlu diserahkan</span>
            </div>
          </a>
        @empty
          <p class="px-5 pb-8 pt-2 text-center text-body-md text-on-surface-variant">Belum ada pesanan baru.</p>
        @endforelse
      </div>
    </section>

    <section class="panel flex flex-col overflow-hidden lg:col-span-5">
      <div class="flex items-center justify-between gap-2 p-5">
        <div class="flex items-center gap-2">
          <h2 class="text-headline-sm font-bold">Stok Menipis</h2>
          @if ($lowStockCount > 0)<span class="rounded-full bg-error-container px-2 py-0.5 text-label-sm font-bold text-error">{{ $lowStockCount }} produk</span>@endif
        </div>
        <a href="{{ route('seller.stock.index') }}" class="flex items-center gap-0.5 text-label-md font-semibold text-primary hover:underline">
          Isi stok <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        </a>
      </div>
      <div class="flex flex-col">
        @forelse ($lowStock as $p)
          <a href="{{ route('seller.stock.index') }}" class="flex items-center justify-between gap-3 p-4 transition-colors hover:bg-surface-container-low">
            <div class="flex min-w-0 items-center gap-3">
              <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $p->stock == 0 ? 'bg-error-container/60 text-error' : 'bg-secondary-fixed text-secondary' }}">
                <span class="material-symbols-outlined text-[20px]">{{ $p->stock == 0 ? 'production_quantity_limits' : 'inventory' }}</span>
              </div>
              <span class="truncate text-label-lg font-bold">{{ $p->name }}</span>
            </div>
            <span class="shrink-0 rounded-full px-2 py-0.5 text-label-sm font-bold {{ $p->stock == 0 ? 'bg-error-container text-error' : 'bg-secondary-fixed text-on-secondary-fixed' }}">
              {{ $p->stock == 0 ? 'Habis' : 'Sisa ' . $p->stock }}
            </span>
          </a>
        @empty
          <p class="px-5 pb-8 pt-2 text-center text-body-md text-on-surface-variant">Semua stok aman.</p>
        @endforelse
      </div>
    </section>
  </div>

  {{-- Kasbon --}}
  <a href="{{ route('seller.kasbon.index') }}" class="panel flex items-center justify-between gap-4 p-5 transition-shadow hover:shadow-md">
    <div class="flex items-center gap-4">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-secondary-fixed text-secondary"><span class="material-symbols-outlined text-[26px]">menu_book</span></div>
      <div class="flex flex-col">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-headline-sm font-bold">Buku Kasbon</span>
          <span class="rounded-full bg-secondary-fixed px-2 py-0.5 text-label-sm font-bold text-on-secondary-fixed">{{ $kasbonCount }} pelanggan</span>
        </div>
        <span class="text-body-sm text-on-surface-variant">Total kasbon belum dibayar: <strong class="font-bold text-on-surface">{{ $rp($kasbonTotal) }}</strong></span>
      </div>
    </div>
    <span class="material-symbols-outlined shrink-0 text-primary">chevron_right</span>
  </a>
</div>
@endsection
