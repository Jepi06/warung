@extends('layouts.seller')
@section('title', $type === 'out' ? 'Catat pengeluaran' : 'Catat pemasukan')

@section('content')
<h1 class="mb-4 text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">{{ $type === 'out' ? 'Catat pengeluaran' : 'Catat pemasukan lain' }}</h1>

<form method="POST" action="{{ route('seller.transactions.store') }}" class="panel max-w-lg space-y-4 p-5">
  @csrf
  <input type="hidden" name="type" value="{{ $type }}">
  <div>
    <label class="label" for="description">Keterangan</label>
    <input id="description" name="description" value="{{ old('description') }}" required class="input"
           placeholder="{{ $type === 'out' ? 'Contoh: Belanja terigu di pasar' : 'Contoh: Titip jual' }}">
  </div>
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="label" for="amount">Jumlah uang (Rp)</label>
      <input id="amount" type="number" min="1" name="amount" value="{{ old('amount') }}" required class="input">
    </div>
    <div>
      <label class="label" for="date">Tanggal</label>
      <input id="date" type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required class="input">
    </div>
  </div>

  @if ($type === 'out')
    <div class="rounded-lg border border-outline-variant/40 bg-surface p-4">
      <div class="mb-3 text-sm font-semibold">Belanja barang dagangan? Tambahkan ke stok</div>
      <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
          <label class="label" for="product_id">Barang</label>
          <select id="product_id" name="product_id" class="input">
            <option value="">Bukan barang dagangan</option>
            @foreach ($products as $p)
              <option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>{{ $p->name }} (stok {{ $p->stock }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label" for="qty">Jumlah</label>
          <input id="qty" type="number" min="1" name="qty" value="{{ old('qty') }}" class="input" placeholder="5">
        </div>
      </div>
      <p class="mt-2 text-xs text-on-surface-variant">Jumlah mengikuti satuan stok barang tersebut. Kalau stok terigu dihitung per kg, isi 5 untuk 5 kg.</p>
    </div>
  @endif

  <div class="flex gap-2">
    <button class="btn-primary">Simpan</button>
    <a href="{{ route('seller.transactions.index') }}" class="btn-ghost">Batal</a>
  </div>
</form>
@endsection
