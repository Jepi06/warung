<!DOCTYPE html>
<html lang="id">
<head>
  @include('partials.head')
  <title>@yield('title', 'Penjual') · {{ config('shop.name') }}</title>
</head>
<body class="min-h-screen bg-surface font-sans text-body-md text-on-surface antialiased" x-data="{ more: false }">
@php
  $pending = \App\Models\Order::where('status', 'pending')->count();
  $nav = [
    ['seller.dashboard',          'Ringkasan',    'Ringkasan', 'grid_view',              'seller.dashboard'],
    ['seller.kasir.index',        'Kasir',        'Kasir',     'point_of_sale',          'seller.kasir.*'],
    ['seller.orders.index',       'Pesanan',      'Pesanan',   'receipt_long',           'seller.orders.*'],
    ['seller.kasbon.index',       'Kasbon',       'Kasbon',    'menu_book',              'seller.kasbon.*'],
    ['seller.products.index',     'Barang',       'Barang',    'inventory_2',            'seller.products.*'],
    ['seller.stock.index',        'Stok & harga', 'Stok',      'inventory',              'seller.stock.*'],
    ['seller.transactions.index', 'Kas',          'Kas',       'account_balance_wallet', 'seller.transactions.*'],
  ];
  $mainNav = array_slice($nav, 0, 4);
  $moreNav = array_slice($nav, 4);
  $moreActive = collect($moreNav)->contains(fn ($n) => request()->routeIs($n[4]));
@endphp

  {{-- Sidebar (desktop) --}}
  <aside class="fixed left-0 top-0 z-50 hidden h-full w-64 flex-col justify-between bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] md:flex">
    <div class="flex flex-col">
      <div class="flex h-16 items-center gap-2 px-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('shop.name') }}" class="h-10 w-10 shrink-0 rounded-full shadow-sm">
        <div class="flex min-w-0 flex-col">
          <span class="truncate text-headline-sm font-bold leading-tight text-primary">{{ config('shop.name') }}</span>
          <span class="text-label-sm font-medium text-on-surface-variant">Panel penjual</span>
        </div>
      </div>

      <a href="{{ route('shop') }}" target="_blank"
         class="mx-4 my-2 flex items-center justify-between rounded-xl bg-surface-container-low p-2 text-label-md font-semibold text-primary hover:bg-surface-container-high">
        <span>Lihat tampilan toko</span>
        <span class="material-symbols-outlined text-[16px]">open_in_new</span>
      </a>

      <nav class="mt-1 flex flex-col gap-1 px-2" aria-label="Menu utama">
        @foreach ($nav as [$route, $label, $short, $icon, $pattern])
          @php $on = request()->routeIs($pattern); @endphp
          <a href="{{ route($route) }}" @if ($on) aria-current="page" @endif
             class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-label-lg transition-all {{ $on ? 'bg-primary-container font-bold text-on-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
            <span class="material-symbols-outlined text-[20px]" @if ($on) style="font-variation-settings:'FILL' 1" @endif>{{ $icon }}</span>
            {{ $label }}
            @if ($route === 'seller.orders.index' && $pending > 0)
              <span class="ml-auto rounded-full bg-secondary-container px-2 text-label-sm text-on-secondary-container">{{ $pending }}</span>
            @endif
          </a>
        @endforeach
      </nav>
    </div>

    <div class="p-4">
      <form method="POST" action="{{ route('seller.logout') }}">@csrf
        <button class="flex w-full items-center gap-2 rounded-xl px-4 py-2.5 text-label-lg text-on-surface-variant hover:bg-surface-container-high">
          <span class="material-symbols-outlined text-[20px]">logout</span> Keluar
        </button>
      </form>
    </div>
  </aside>

  {{-- Header --}}
  <header class="fixed left-0 right-0 top-0 z-40 flex h-16 items-center justify-between bg-surface-container-lowest/90 px-4 shadow-[0_1px_8px_rgba(0,0,0,0.04)] backdrop-blur-xl md:left-64 md:px-6">
    <div class="flex min-w-0 items-center gap-2">
      <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('shop.name') }}" class="h-9 w-9 shrink-0 rounded-full md:hidden">
      <span class="truncate text-headline-sm font-bold text-primary md:hidden">{{ config('shop.name') }}</span>
      <span class="hidden text-label-lg text-on-surface-variant md:inline">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('seller.orders.index') }}" aria-label="Pesanan menunggu"
         class="relative rounded-xl bg-surface-container-low p-2 text-on-surface hover:bg-surface-container-high">
        <span class="material-symbols-outlined text-[20px]">notifications</span>
        @if ($pending > 0)
          <span class="absolute -right-1 -top-1 min-w-4 rounded-full bg-secondary-container px-1 text-center text-[10px] font-bold leading-4 text-on-secondary-container">{{ $pending }}</span>
        @endif
      </a>
      <div class="hidden h-8 w-px bg-surface-container-high md:block"></div>
      <div class="hidden items-center gap-2 md:flex">
        <div class="flex flex-col text-right">
          <span class="text-label-md font-bold leading-tight">{{ config('shop.name') }}</span>
          <span class="text-label-sm font-medium text-on-surface-variant">Penjual</span>
        </div>
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary">
          <span class="material-symbols-outlined text-[18px] text-on-primary">person</span>
        </div>
      </div>
    </div>
  </header>

  <div class="md:pl-64">
    <main class="mx-auto max-w-6xl px-4 pb-28 pt-20 md:px-6 md:pb-10 md:pt-24">
      @include('partials.flash')
      @yield('content')
    </main>
  </div>

  {{-- Menu bawah (HP) --}}
  <nav class="fixed inset-x-0 bottom-0 z-50 grid h-16 grid-cols-5 bg-surface-container-lowest shadow-[0_-1px_8px_rgba(0,0,0,0.06)] md:hidden" aria-label="Menu utama">
    @foreach ($mainNav as [$route, $label, $short, $icon, $pattern])
      @php $on = request()->routeIs($pattern); @endphp
      <a href="{{ route($route) }}" @if ($on) aria-current="page" @endif
         class="relative flex flex-col items-center justify-center gap-0.5 text-[11px] font-semibold {{ $on ? 'text-primary' : 'text-on-surface-variant' }}">
        <span class="material-symbols-outlined text-[24px]" @if ($on) style="font-variation-settings:'FILL' 1" @endif>{{ $icon }}</span>
        {{ $short }}
        @if ($route === 'seller.orders.index' && $pending > 0)
          <span class="absolute right-3 top-1.5 min-w-4 rounded-full bg-secondary-container px-1 text-center text-[10px] font-bold leading-4 text-on-secondary-container">{{ $pending }}</span>
        @endif
      </a>
    @endforeach
    <button type="button" @click="more = !more"
            class="flex flex-col items-center justify-center gap-0.5 text-[11px] font-semibold {{ $moreActive ? 'text-primary' : 'text-on-surface-variant' }}">
      <span class="material-symbols-outlined text-[24px]">more_horiz</span>
      Lainnya
    </button>
  </nav>

  <div x-show="more" x-cloak class="fixed inset-0 z-[45] md:hidden" @keydown.escape.window="more = false">
    <div class="absolute inset-0 bg-inverse-surface/40" @click="more = false"></div>
    <div class="absolute inset-x-0 bottom-16 rounded-t-2xl bg-surface-container-lowest p-3 shadow-lg">
      @foreach ($moreNav as [$route, $label, $short, $icon, $pattern])
        <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-label-lg {{ request()->routeIs($pattern) ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface hover:bg-surface-container-low' }}">
          <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span> {{ $label }}
        </a>
      @endforeach
      <a href="{{ route('shop') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-3 text-label-lg text-on-surface hover:bg-surface-container-low">
        <span class="material-symbols-outlined text-[22px]">storefront</span> Lihat tampilan toko
      </a>
      <form method="POST" action="{{ route('seller.logout') }}">@csrf
        <button class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-label-lg text-error hover:bg-error-container/40">
          <span class="material-symbols-outlined text-[22px]">logout</span> Keluar
        </button>
      </form>
    </div>
  </div>
</body>
</html>
