@extends('layouts.seller')
@section('title', $product->exists ? 'Ubah barang' : 'Tambah barang')

@section('content')
<h1 class="mb-4 text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">{{ $product->exists ? 'Ubah barang' : 'Tambah barang' }}</h1>

<form method="POST" enctype="multipart/form-data"
      action="{{ $product->exists ? route('seller.products.update', $product) : route('seller.products.store') }}"
      class="panel max-w-lg space-y-4 p-5">
  @csrf
  @if ($product->exists) @method('PUT') @endif

  <div>
    <label class="label" for="name">Nama barang</label>
    <input id="name" name="name" value="{{ old('name', $product->name) }}" required class="input">
  </div>
  <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
    <div>
      <label class="label" for="cost_price">Harga beli (Rp)</label>
      <input id="cost_price" type="number" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}" required class="input">
    </div>
    <div>
      <label class="label" for="price">Harga jual (Rp)</label>
      <input id="price" type="number" min="0" name="price" value="{{ old('price', $product->price) }}" required class="input">
    </div>
    <div>
      <label class="label" for="stock">Stok</label>
      <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="input">
    </div>
  </div>
  <div>
    <label class="label" for="image">Foto (opsional, maks 2 MB)</label>
    @if ($product->imageUrl())
      <img src="{{ $product->imageUrl() }}" alt="" class="mb-2 h-20 w-20 rounded-lg object-cover">
    @endif
    <input id="image" type="file" name="image" accept="image/*" class="input">
  </div>
  <div class="flex gap-2">
    <button class="btn-primary">Simpan</button>
    <a href="{{ route('seller.products.index') }}" class="btn-ghost">Batal</a>
  </div>
</form>
@endsection
