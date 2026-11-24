@extends('layouts.seller')
@section('title', 'Kas')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
  <h1 class="text-headline-lg-mobile font-extrabold tracking-tight md:text-headline-lg">Kas masuk &amp; keluar</h1>
  <div class="flex w-full gap-2 sm:w-auto">
    <a href="{{ route('seller.transactions.create', ['type' => 'out']) }}" class="btn-primary flex-1 sm:flex-none">Catat pengeluaran</a>
    <a href="{{ route('seller.transactions.create', ['type' => 'in']) }}" class="btn-ghost flex-1 sm:flex-none">Pemasukan lain</a>
  </div>
</div>

<form method="GET" class="mb-4 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-end">
  <div><label class="label" for="from">Dari</label><input id="from" type="date" name="from" value="{{ $from->toDateString() }}" class="input"></div>
  <div><label class="label" for="to">Sampai</label><input id="to" type="date" name="to" value="{{ $to->toDateString() }}" class="input"></div>
  <div>
    <label class="label" for="type">Jenis</label>
    <select id="type" name="type" class="input">
      <option value="">Semua</option>
      <option value="in" @selected($type === 'in')>Pemasukan</option>
      <option value="out" @selected($type === 'out')>Pengeluaran</option>
    </select>
  </div>
  <button class="btn-ghost self-end">Tampilkan</button>
</form>

@php $diff = $income - $expense; @endphp
<div class="mb-4 grid gap-3 sm:grid-cols-3">
  <div class="panel p-4"><div class="text-sm text-on-surface-variant">Pemasukan</div><div class="text-xl font-bold text-primary">Rp {{ number_format($income, 0, ',', '.') }}</div></div>
  <div class="panel p-4"><div class="text-sm text-on-surface-variant">Pengeluaran</div><div class="text-xl font-bold text-error">Rp {{ number_format($expense, 0, ',', '.') }}</div></div>
  <div class="panel p-4"><div class="text-sm text-on-surface-variant">Selisih</div><div class="text-xl font-bold {{ $diff < 0 ? 'text-error' : '' }}">{{ $diff < 0 ? '−' : '' }}Rp {{ number_format(abs($diff), 0, ',', '.') }}</div></div>
</div>

<div class="panel">
  <table class="rtable w-full">
    <thead class="border-b border-outline-variant/40"><tr>
      <th class="th">Tanggal</th><th class="th">Keterangan</th><th class="th text-right">Jumlah</th><th class="th"></th>
    </tr></thead>
    <tbody class="divide-y divide-outline-variant/30">
      @forelse ($transactions as $t)
        <tr>
          <td class="td whitespace-nowrap text-on-surface-variant" data-label="Tanggal">{{ $t->date->locale('id')->translatedFormat('d M Y') }}</td>
          <td class="td" data-label="Keterangan">
            <div>
              {{ $t->description }}
              @if ($t->product && $t->qty)
                <span class="ml-1 rounded-full bg-primary-fixed/50 px-2 py-0.5 text-xs font-semibold text-on-primary-fixed">+{{ $t->qty }} stok {{ $t->product->name }}</span>
              @endif
              @if ($t->debt_entry_id)
                <span class="ml-1 rounded-full bg-secondary-fixed px-2 py-0.5 text-label-sm font-bold text-on-secondary-fixed">kasbon</span>
              @endif
              @if ($t->order_id)
                <a href="{{ route('seller.orders.show', $t->order_id) }}" class="ml-1 text-xs text-primary hover:underline">lihat pesanan</a>
              @endif
            </div>
          </td>
          <td class="td whitespace-nowrap text-right font-semibold {{ $t->type === 'in' ? 'text-primary' : 'text-error' }}" data-label="Jumlah">
            {{ $t->type === 'in' ? '+' : '−' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
          </td>
          <td class="td rt-actions text-right" data-label="">
            @unless ($t->order_id || $t->debt_entry_id)
              <form method="POST" action="{{ route('seller.transactions.destroy', $t) }}" onsubmit="return confirm('Hapus catatan ini?')">
                @csrf @method('DELETE')
                <button class="text-sm text-error hover:underline">Hapus</button>
              </form>
            @endunless
          </td>
        </tr>
      @empty
        <tr><td colspan="4" data-label="" class="rt-empty px-4 py-10 text-center text-sm text-on-surface-variant">Belum ada transaksi di rentang tanggal ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
