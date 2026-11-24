<!DOCTYPE html>
<html lang="id">
<head>
  @include('partials.head')
  <title>@yield('title', config('shop.name'))</title>
  <meta property="og:title" content="{{ config('shop.name') }}">
  <meta property="og:description" content="Belanja sembako dan pesan lewat WhatsApp.">
  <meta property="og:image" content="{{ asset('images/logo.png') }}">
</head>
<body class="min-h-screen bg-surface font-sans text-body-md text-on-surface antialiased">
  <header class="bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-3">
      <a href="{{ route('shop') }}" class="flex min-w-0 items-center gap-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('shop.name') }}" class="h-11 w-11 shrink-0 rounded-full shadow-sm">
        <span class="truncate text-headline-sm font-bold text-primary">{{ config('shop.name') }}</span>
      </a>
      <a href="{{ route('seller.login') }}" class="text-label-md text-on-surface-variant hover:text-on-surface">Penjual</a>
    </div>
  </header>
  <main class="mx-auto max-w-3xl px-4 py-6">
    @yield('content')
  </main>
</body>
</html>
