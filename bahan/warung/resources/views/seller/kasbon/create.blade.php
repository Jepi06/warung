@extends('layouts.seller')
@section('title', 'Catat kasbon')

@section('content')
<a href="{{ route('seller.kasbon.index') }}" class="inline-flex items-center gap-1 text-label-md font-semibold text-primary hover:underline">
  <span class="material-symbols-outlined text-[16px]">arrow_back</span> Buku kasbon
</a>
<h1 class="mb-1 mt-2 text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Catat kasbon</h1>
<p class="mb-4 text-body-md text-on-surface-variant">Untuk utang yang tidak lewat kasir, misalnya utang lama atau pinjaman uang. Belanja lewat kasir otomatis tercatat saat memilih Kasbon.</p>

<form method="POST" action="{{ route('seller.kasbon.store') }}" class="panel max-w-lg space-y-4 p-5">
  @csrf
  <div>
    <label class="label" for="name">Nama pelanggan</label>
    <input id="name" name="name" list="debtor-names" value="{{ old('name') }}" required autocomplete="off" class="input" placeholder="Ketik nama atau pilih">
    <datalist id="debtor-names">@foreach ($names as $n)<option value="{{ $n }}">@endforeach</datalist>
  </div>
  <div>
    <label class="label" for="phone">No. WA (opsional, untuk kirim pengingat)</label>
    <input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" class="input" placeholder="08xxxxxxxxxx">
  </div>
  <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
    <div>
      <label class="label" for="amount">Jumlah (Rp)</label>
      <input id="amount" type="number" min="1" name="amount" value="{{ old('amount') }}" required inputmode="numeric" class="input">
    </div>
    <div>
      <label class="label" for="date">Tanggal</label>
      <input id="date" type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required class="input">
    </div>
  </div>
  <div>
    <label class="label" for="note">Keterangan</label>
    <input id="note" name="note" value="{{ old('note') }}" class="input" placeholder="Contoh: utang bulan lalu">
  </div>
  <div class="flex gap-2">
    <button class="btn-primary">Simpan</button>
    <a href="{{ route('seller.kasbon.index') }}" class="btn-ghost">Batal</a>
  </div>
</form>
@endsection
