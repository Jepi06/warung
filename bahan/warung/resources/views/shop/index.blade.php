@extends('layouts.shop')

@section('content')
@php
  $list = $products->map(fn ($p) => [
      'id' => $p->id, 'name' => $p->name, 'price' => $p->price,
      'stock' => $p->stock, 'image' => $p->imageUrl(),
  ])->values();
@endphp

<form method="POST" action="{{ route('checkout') }}" class="pb-28"
  x-data="{
    products: @js($list), qty: {}, q: '', open: false,
    init() { this.products.forEach(p => this.qty[p.id] = 0) },
    get total() { return this.products.reduce((s, p) => s + p.price * (this.qty[p.id] || 0), 0) },
    get count() { return this.products.reduce((s, p) => s + (this.qty[p.id] || 0), 0) },
    get chosen() { return this.products.filter(p => (this.qty[p.id] || 0) > 0) },
    add(p) { if ((this.qty[p.id] || 0) < p.stock) this.qty[p.id]++ },
    sub(p) { if (this.qty[p.id] > 0) this.qty[p.id]-- },
    rp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID') }
  }">
  @csrf

  @include('partials.flash')

  <div class="relative mb-4">
    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-outline">search</span>
    <input type="search" x-model="q" placeholder="Cari barang…" class="input !pl-9">
  </div>

  <div class="space-y-2">
    <template x-for="p in products" :key="p.id">
      <div x-show="p.name.toLowerCase().includes(q.toLowerCase())" class="panel flex items-center gap-3 p-3">
        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-primary-fixed/40 text-primary">
          <img x-show="p.image" :src="p.image" :alt="p.name" class="h-full w-full object-cover">
          <span x-show="!p.image" class="material-symbols-outlined">inventory_2</span>
        </div>
        <div class="min-w-0 flex-1">
          <div class="truncate text-label-lg font-bold" x-text="p.name"></div>
          <div class="text-label-md text-primary" x-text="rp(p.price)"></div>
          <div class="text-label-sm" :class="p.stock <= 0 ? 'text-error' : 'text-on-surface-variant'"
               x-text="p.stock <= 0 ? 'Habis' : 'Stok ' + p.stock"></div>
        </div>
        <div class="flex items-center gap-2" x-show="p.stock > 0">
          <button type="button" @click="sub(p)" class="btn-ghost !px-3 !py-1.5" :disabled="!qty[p.id]" aria-label="Kurangi">−</button>
          <span class="w-6 text-center text-label-lg" x-text="qty[p.id] || 0"></span>
          <button type="button" @click="add(p)" class="btn-primary !px-3 !py-1.5" :disabled="qty[p.id] >= p.stock" aria-label="Tambah">+</button>
        </div>
      </div>
    </template>
    <p x-show="products.length === 0" class="py-10 text-center text-on-surface-variant">Belum ada barang yang dijual.</p>
  </div>

  <template x-for="p in products" :key="'h' + p.id">
    <input type="hidden" :name="'items[' + p.id + ']'" :value="qty[p.id] || 0">
  </template>

  {{-- Bar total --}}
  <div x-show="count > 0" x-cloak class="fixed inset-x-0 bottom-0 z-40 bg-surface-container-lowest/95 shadow-[0_-2px_12px_rgba(0,0,0,0.08)] backdrop-blur">
    <div class="mx-auto flex max-w-3xl items-center justify-between gap-4 px-4 py-3">
      <div>
        <div class="text-label-sm text-on-surface-variant"><span x-text="count"></span> barang</div>
        <div class="text-headline-sm font-extrabold" x-text="rp(total)"></div>
      </div>
      <button type="button" @click="open = true" class="btn-primary !px-6">Pesan</button>
    </div>
  </div>

  {{-- Konfirmasi pesanan: hanya nama --}}
  <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center bg-inverse-surface/50 sm:items-center" @keydown.escape.window="open = false">
    <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl bg-surface-container-lowest p-5 sm:rounded-2xl" @click.outside="open = false">
      <h2 class="text-headline-sm font-bold">Pesanan kamu</h2>
      <ul class="mt-3 divide-y divide-outline-variant/30 text-body-md">
        <template x-for="p in chosen" :key="'c' + p.id">
          <li class="flex justify-between gap-3 py-2">
            <span><span x-text="p.name"></span> <span class="text-on-surface-variant">×<span x-text="qty[p.id]"></span></span></span>
            <span class="shrink-0" x-text="rp(p.price * qty[p.id])"></span>
          </li>
        </template>
      </ul>
      <div class="mt-1 flex justify-between border-t border-outline-variant/40 pt-3 text-headline-sm font-extrabold">
        <span>Total</span><span x-text="rp(total)"></span>
      </div>

      <div class="mt-4">
        <label class="label" for="customer_name">Nama</label>
        <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required class="input" placeholder="Nama kamu">
      </div>

      <div class="mt-5 flex gap-2">
        <button type="button" @click="open = false" class="btn-ghost flex-1">Kembali</button>
        <button type="submit" class="btn-primary flex-1">Kirim lewat WhatsApp</button>
      </div>
    </div>
  </div>
</form>
@endsection
