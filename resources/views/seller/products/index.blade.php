@extends('layouts.seller')
@section('title', 'Barang')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
  <h1 class="text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Barang</h1>
  <div class="flex w-full gap-2 sm:w-auto">
    <form method="GET" class="flex-1"><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari barang…" class="input"></form>
    <a href="{{ route('seller.products.create') }}" class="btn-primary whitespace-nowrap">Tambah barang</a>
  </div>
</div>

<div class="panel">
  <table class="rtable w-full">
    <thead class="border-b border-outline-variant/40"><tr>
      <th class="th">Barang</th><th class="th">Harga beli</th><th class="th">Harga jual</th><th class="th">Stok</th><th class="th"></th>
    </tr></thead>
    <tbody class="divide-y divide-outline-variant/30">
      @forelse ($products as $p)
        <tr>
          <td class="td rt-title" data-label="">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-primary-fixed/50">
                @if ($p->imageUrl())<img src="{{ $p->imageUrl() }}" alt="" class="h-full w-full object-cover">@endif
              </div>
              <span class="font-medium">{{ $p->name }}</span>
            </div>
          </td>
          <td class="td text-on-surface-variant" data-label="Harga beli">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
          <td class="td" data-label="Harga jual">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
          <td class="td" data-label="Stok">
            <span class="font-semibold {{ $p->stock == 0 ? 'text-error' : ($p->stock <= 5 ? 'text-secondary' : '') }}">{{ $p->stock }}</span>
          </td>
          <td class="td rt-actions" data-label="">
            <div class="flex justify-end gap-2">
              <a href="{{ route('seller.products.edit', $p) }}" class="btn-ghost !px-3 !py-1.5">Ubah</a>
              <form method="POST" action="{{ route('seller.products.destroy', $p) }}" onsubmit="return confirm('Hapus {{ addslashes($p->name) }}?')">
                @csrf @method('DELETE')
                <button class="btn-danger !px-3 !py-1.5">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" data-label="" class="rt-empty px-4 py-10 text-center text-sm text-on-surface-variant">Belum ada barang. Tambahkan barang pertama.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
