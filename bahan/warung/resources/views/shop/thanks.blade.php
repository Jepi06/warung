@extends('layouts.shop')
@section('title', 'Pesanan #' . $order->id)

@section('content')
<div class="panel p-5">
  <h1 class="text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Pesanan #{{ $order->id }} tersimpan</h1>
  <p class="mt-1 text-sm text-on-surface-variant">
    Kirim pesan WhatsApp ke penjual supaya pesananmu diproses. Pesanan baru selesai setelah penjual mengkonfirmasi.
  </p>

  <ul class="mt-4 divide-y divide-outline-variant/30 text-sm">
    @foreach ($order->items as $i)
      <li class="flex justify-between py-2">
        <span>{{ $i->product_name }} <span class="text-on-surface-variant">×{{ $i->qty }}</span></span>
        <span>Rp {{ number_format($i->subtotal, 0, ',', '.') }}</span>
      </li>
    @endforeach
  </ul>
  <div class="flex justify-between border-t border-outline-variant/40 pt-3 font-bold">
    <span>Total</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
  </div>

  <a href="{{ $order->waUrl() }}" id="wa" class="btn-primary mt-5 w-full">Buka WhatsApp</a>
  <a href="{{ route('shop') }}" class="btn-ghost mt-2 w-full">Kembali ke daftar barang</a>
  <p class="mt-3 text-xs text-on-surface-variant">WhatsApp akan terbuka otomatis. Kalau tidak, tekan tombol di atas.</p>
</div>

<script>
  setTimeout(() => { window.location.href = document.getElementById('wa').href }, 1200);
</script>
@endsection
