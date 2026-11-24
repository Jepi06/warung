@extends('layouts.seller')
@section('title', 'Pesanan')

@section('content')
<h1 class="mb-4 text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Pesanan</h1>

<div class="-mx-4 mb-4 flex gap-1 overflow-x-auto px-4 text-sm font-medium">
  @foreach (['pending' => 'Menunggu', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan', 'all' => 'Semua'] as $key => $label)
    <a href="{{ route('seller.orders.index', ['status' => $key]) }}"
       class="whitespace-nowrap rounded-lg px-3 py-1.5 {{ $status === $key ? 'bg-primary text-white' : 'border border-outline-variant/40 bg-surface-container-lowest hover:bg-primary-fixed/50' }}">{{ $label }}</a>
  @endforeach
</div>

<div class="panel">
  <table class="rtable w-full">
    <thead class="border-b border-outline-variant/40"><tr>
      <th class="th">No.</th><th class="th">Pembeli</th><th class="th">Waktu</th>
      <th class="th">Total</th><th class="th">Status</th><th class="th"></th>
    </tr></thead>
    <tbody class="divide-y divide-outline-variant/30">
      @forelse ($orders as $o)
        <tr>
          <td class="td rt-title font-medium" data-label="">#{{ $o->id }} · {{ $o->customer_name }}</td>
          <td class="td" data-label="Barang">{{ $o->items_count }} jenis</td>
          <td class="td text-on-surface-variant" data-label="Waktu">{{ $o->created_at->locale('id')->translatedFormat('d M, H:i') }}</td>
          <td class="td font-semibold" data-label="Total">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
          <td class="td" data-label="Status">@include('partials.status', ['status' => $o->status])</td>
          <td class="td rt-actions text-right" data-label=""><a href="{{ route('seller.orders.show', $o) }}" class="btn-ghost !px-3 !py-1.5">Buka</a></td>
        </tr>
      @empty
        <tr><td colspan="6" data-label="" class="rt-empty px-4 py-10 text-center text-sm text-on-surface-variant">Tidak ada pesanan.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
