@extends('layouts.seller')
@section('title', 'Pesanan #' . $order->id)

@section('content')
<a href="{{ route('seller.orders.index') }}" class="text-sm text-primary hover:underline">← Semua pesanan</a>

<div class="mb-4 mt-2 flex flex-wrap items-center gap-3">
  <h1 class="text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Pesanan #{{ $order->id }}</h1>
  @include('partials.status', ['status' => $order->status])
</div>

<div class="grid gap-6 lg:grid-cols-3">
  <div class="panel lg:col-span-2">
    <table class="rtable w-full">
      <thead class="border-b border-outline-variant/40"><tr>
        <th class="th">Barang</th><th class="th">Harga</th><th class="th">Jumlah</th><th class="th text-right">Subtotal</th>
      </tr></thead>
      <tbody class="divide-y divide-outline-variant/30">
        @foreach ($order->items as $i)
          @php $stok = $i->product?->stock; @endphp
          <tr>
            <td class="td rt-title font-medium" data-label="">
              <div>
                {{ $i->product_name }}
                @if ($order->status === 'pending')
                  <div class="text-xs font-normal {{ ($stok ?? 0) < $i->qty ? 'text-error' : 'text-on-surface-variant' }}">
                    {{ $i->product ? 'Stok sekarang ' . $stok : 'Barang sudah dihapus' }}
                  </div>
                @endif
              </div>
            </td>
            <td class="td" data-label="Harga">Rp {{ number_format($i->price, 0, ',', '.') }}</td>
            <td class="td" data-label="Jumlah">{{ $i->qty }}</td>
            <td class="td text-right" data-label="Subtotal">Rp {{ number_format($i->subtotal, 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot class="border-t border-outline-variant/40">
        <tr>
          <td colspan="3" class="td !hidden text-right font-semibold md:!table-cell">Total</td>
          <td class="td text-right text-base font-bold" data-label="Total">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="space-y-4">
    <div class="panel p-4 text-sm">
      <div class="text-on-surface-variant">Pembeli</div>
      <div class="font-semibold">{{ $order->customer_name }}</div>
      @if ($order->customer_phone)
        <a href="{{ $order->customerWaUrl() }}" target="_blank" class="text-primary hover:underline">{{ $order->customer_phone }} (chat WA)</a>
      @endif
      @if ($order->note)
        <div class="mt-3 text-on-surface-variant">Catatan</div><div>{{ $order->note }}</div>
      @endif
      <div class="mt-3 text-on-surface-variant">Dipesan</div><div>{{ $order->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</div>
      @if ($order->confirmed_at)
        <div class="mt-3 text-on-surface-variant">Diserahkan</div><div>{{ $order->confirmed_at->locale('id')->translatedFormat('d M Y, H:i') }}</div>
      @endif
      @if ($order->status === 'completed')
        @php $laba = $order->items->sum(fn ($i) => ($i->price - $i->cost) * $i->qty); @endphp
        <div class="mt-3 text-on-surface-variant">Laba pesanan ini</div>
        <div class="font-semibold {{ $laba < 0 ? 'text-error' : 'text-primary' }}">{{ $laba < 0 ? '−' : '' }}Rp {{ number_format(abs($laba), 0, ',', '.') }}</div>
      @endif
    </div>

    @if ($order->status === 'pending')
      <div class="panel space-y-2 p-4">
        <form method="POST" action="{{ route('seller.orders.confirm', $order) }}"
              onsubmit="return confirm('Barang sudah diserahkan ke pembeli? Stok akan dikurangi.')">
          @csrf
          <button class="btn-primary w-full">Barang sudah diserahkan</button>
        </form>
        <form method="POST" action="{{ route('seller.orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
          @csrf
          <button class="btn-danger w-full">Batalkan pesanan</button>
        </form>
        <p class="text-xs text-on-surface-variant">Stok berkurang dan pemasukan tercatat setelah kamu menekan tombol hijau.</p>
      </div>
    @endif
  </div>
</div>
@endsection
