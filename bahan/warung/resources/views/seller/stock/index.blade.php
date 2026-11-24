@extends('layouts.seller')
@section('title', 'Stok & harga')

@section('content')
@php
  $list = $products->map(fn ($p) => [
      'id' => $p->id, 'name' => $p->name,
      'stock' => $p->stock, 'cost' => $p->cost_price, 'price' => $p->price,
  ])->values();
@endphp

<h1 class="text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Stok &amp; harga</h1>
<p class="mb-4 mt-1 max-w-2xl text-sm text-on-surface-variant">
  Ubah angka langsung di tabel. Hanya barang yang berubah yang disimpan.
  Kalau stok dinaikkan, selisihnya otomatis dicatat sebagai pengeluaran sebesar selisih × harga beli.
  Kalau stok diturunkan, dianggap koreksi dan tidak mengubah kas.
</p>

<form method="POST" action="{{ route('seller.stock.update') }}" class="pb-24"
  x-data="{
    rows: @js($list).map(r => Object.assign({}, r, { stock0: r.stock, cost0: r.cost, price0: r.price })),
    q: '',
    date: '{{ now()->toDateString() }}',
    n(v) { return Math.max(0, Math.floor(Number(v) || 0)) },
    diff(r) { return this.n(r.stock) - r.stock0 },
    changed(r) { return this.diff(r) !== 0 || this.n(r.cost) !== r.cost0 || this.n(r.price) !== r.price0 },
    spend(r) { return this.diff(r) > 0 ? this.diff(r) * this.n(r.cost) : 0 },
    get changedRows() { return this.rows.filter(r => this.changed(r)) },
    get totalSpend() { return this.changedRows.reduce((s, r) => s + this.spend(r), 0) },
    rp(v) { return 'Rp ' + Number(v).toLocaleString('id-ID') }
  }">
  @csrf

  <input type="search" x-model="q" placeholder="Cari barang…" class="input mb-3 sm:max-w-xs">

  <div class="panel">
    <table class="rtable w-full">
      <thead class="border-b border-outline-variant/40"><tr>
        <th class="th">Barang</th>
        <th class="th">Stok</th>
        <th class="th">Harga beli</th>
        <th class="th">Harga jual</th>
        <th class="th text-right">Pengeluaran</th>
      </tr></thead>
      <tbody class="divide-y divide-outline-variant/30">
        <template x-for="r in rows" :key="r.id">
          <tr x-show="r.name.toLowerCase().includes(q.toLowerCase())" :class="changed(r) ? 'bg-secondary-fixed/50' : ''">
            <td class="td rt-title font-medium" data-label="" x-text="r.name"></td>
            <td class="td" data-label="Stok">
              <div>
                <div class="flex items-center justify-end gap-2 md:justify-start">
                  <input type="number" min="0" x-model.number="r.stock" class="input !w-24" :aria-label="'Stok ' + r.name">
                  <span class="text-xs font-semibold text-primary" x-show="diff(r) > 0" x-text="'+' + diff(r)"></span>
                  <span class="text-xs font-semibold text-secondary" x-show="diff(r) < 0" x-text="'koreksi −' + Math.abs(diff(r))"></span>
                </div>
                <div class="mt-0.5 text-xs text-on-surface-variant" x-show="diff(r) !== 0">tadinya <span x-text="r.stock0"></span></div>
              </div>
            </td>
            <td class="td" data-label="Harga beli">
              <div>
                <input type="number" min="0" x-model.number="r.cost" class="input !w-32" :aria-label="'Harga beli ' + r.name">
                <div class="mt-0.5 text-xs text-error" x-show="diff(r) > 0 && n(r.cost) === 0">isi harga beli agar tercatat</div>
              </div>
            </td>
            <td class="td" data-label="Harga jual">
              <div>
                <input type="number" min="0" x-model.number="r.price" class="input !w-32" :aria-label="'Harga jual ' + r.name">
                <div class="mt-0.5 text-xs text-on-surface-variant" x-show="n(r.price) > 0 && n(r.cost) > 0"
                     x-text="'untung ' + rp(n(r.price) - n(r.cost)) + ' / satuan'"></div>
              </div>
            </td>
            <td class="td text-right font-semibold" data-label="Pengeluaran" x-text="spend(r) > 0 ? rp(spend(r)) : '–'"></td>
          </tr>
        </template>
      </tbody>
    </table>
    <p x-show="rows.length === 0" class="px-4 py-10 text-center text-sm text-on-surface-variant">
      Belum ada barang. Tambahkan dulu di menu Barang.
    </p>
  </div>

  {{-- Hanya barang yang berubah yang dikirim --}}
  <div class="hidden">
    <template x-for="r in changedRows" :key="'h' + r.id">
      <div>
        <input type="hidden" :name="'rows[' + r.id + '][id]'" :value="r.id">
        <input type="hidden" :name="'rows[' + r.id + '][old_stock]'" :value="r.stock0">
        <input type="hidden" :name="'rows[' + r.id + '][stock]'" :value="n(r.stock)">
        <input type="hidden" :name="'rows[' + r.id + '][cost_price]'" :value="n(r.cost)">
        <input type="hidden" :name="'rows[' + r.id + '][price]'" :value="n(r.price)">
      </div>
    </template>
  </div>

  <div x-show="changedRows.length > 0" x-cloak class="fixed inset-x-0 bottom-16 z-40 border-t border-outline-variant/40 bg-surface-container-lowest/95 backdrop-blur md:bottom-0 md:left-64">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3 md:px-6">
      <div>
        <div class="text-xs text-on-surface-variant"><span x-text="changedRows.length"></span> barang berubah</div>
        <div class="font-bold">Pengeluaran <span x-text="rp(totalSpend)"></span></div>
      </div>
      <div class="flex items-center gap-2">
        <input type="date" name="date" x-model="date" required class="input !w-40" aria-label="Tanggal belanja">
        <button class="btn-primary whitespace-nowrap">Simpan</button>
      </div>
    </div>
  </div>
</form>
@endsection
