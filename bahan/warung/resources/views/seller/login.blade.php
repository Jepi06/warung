<!DOCTYPE html>
<html lang="id">
<head>
  @include('partials.head')
  <title>Masuk penjual · {{ config('shop.name') }}</title>
</head>
<body class="min-h-screen bg-surface font-sans text-body-md text-on-surface antialiased">
  <main class="mx-auto flex min-h-screen max-w-sm flex-col justify-center px-4">
    <div class="mb-6 flex items-center gap-3">
      <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('shop.name') }}" class="h-16 w-16 shrink-0 rounded-full shadow-sm">
      <div>
        <h1 class="text-headline-md font-extrabold text-primary">{{ config('shop.name') }}</h1>
        <p class="text-body-sm text-on-surface-variant">Masuk untuk mengelola toko.</p>
      </div>
    </div>
    <form method="POST" action="{{ route('seller.login.post') }}" class="panel space-y-4 p-5">
      @csrf
      @if ($errors->any())
        <div class="flex items-center gap-2 rounded-xl bg-error-container px-3 py-2 text-label-md text-on-error-container">
          <span class="material-symbols-outlined text-[18px]">error</span>{{ $errors->first() }}
        </div>
      @endif
      <div>
        <label for="password" class="label">Password</label>
        <input id="password" type="password" name="password" autofocus required class="input">
      </div>
      <button class="btn-primary w-full">Masuk</button>
    </form>
    <a href="{{ route('shop') }}" class="mt-4 text-center text-label-md text-on-surface-variant hover:text-on-surface">Kembali ke toko</a>
  </main>
</body>
</html>
