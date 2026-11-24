@extends('layouts.seller')
@section('title', $debtor->name)

@section('content')
@php $rp = fn ($n) => 'Rp ' . number_format(abs($n), 0, ',', '.'); @endphp

<a href="{{ route('seller.kasbon.index') }}" class="inline-flex items-center gap-1 text-label-md font-semibold text-primary hover:underline">
  <span class="material-symbols-outlined text-[16px]">arrow_back</span> Buku kasbon
</a>

<div class="mt-3 flex flex-col gap-5">
  {{-- Ringkasan --}}
  <section class="panel flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
    <div class="flex items-center gap-4">
      <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-tertiary-fixed text-headline-sm font-bold text-tertiary">{{ mb_strtoupper(mb_substr($debtor->name, 0, 2)) }}</div>
      <div class="min-w-0">
        <h1 class="truncate text-headline-md font-extrabold">{{ $debtor->name }}</h1>
        @if ($debtor->phone)
          <div class="text-label-md text-on-surface-variant">{{ $debtor->phone }}</div>
        @endif
      </div>
    </div>
    <div class="flex items-center justify-between gap-4 md:justify-end">
      <div class="md:text-right">
        <div class="text-label-sm font-bold uppercase tracking-wider text-on-surface-variant">Sisa kasbon</div>
        <div class="text-currency-stat {{ $balance > 0 ? 'text-error' : 'text-primary' }}">{{ $balance > 0 ? $rp($balance) : 'Lunas' }}</div>
      </div>
      @if ($balance > 0 && $debtor->waReminderUrl())
        <a href="{{ $debtor->waReminderUrl() }}" target="_blank" class="btn-ghost">
          <span class="material-symbols-outlined text-[18px]">send</span> Tagih via WA
        </a>
      @endif
    </div>
  </section>

  {{-- Aksi --}}
  <div class="grid gap-4 md:grid-cols-2">
    @if ($balance > 0)
      <form method="POST" action="{{ route('seller.kasbon.pay', $debtor) }}" class="panel space-y-3 p-5" x-data="{ amount: '{{ old('amount') }}' }">
        @csrf
        <h2 class="text-headline-sm font-bold">Terima pembayaran</h2>
        <div>
          <label class="label" for="pay_amount">Jumlah dibayar (Rp)</label>
          <div class="flex gap-2">
            <input id="pay_amount" type="number" min="1" max="{{ $balance }}" name="amount" x-model="amount" required inputmode="numeric" class="input">
            <button type="button" class="btn-ghost whitespace-nowrap" @click="amount = {{ $balance }}">Lunasi</button>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label" for="pay_date">Tanggal</label>
            <input id="pay_date" type="date" name="date" value="{{ now()->toDateString() }}" required class="input">
          </div>
          <div>
            <label class="label" for="pay_note">Catatan</label>
            <input id="pay_note" name="note" class="input" placeholder="Opsional">
          </div>
        </div>
        <button class="btn-primary w-full">Catat pembayaran</button>
        <p class="text-label-sm text-on-surface-variant">Uang yang diterima otomatis masuk ke Kas sebagai pemasukan.</p>
      </form>
    @else
      <div class="panel flex items-center gap-3 p-5">
        <span class="material-symbols-outlined text-[28px] text-primary" style="font-variation-settings:'FILL' 1">verified</span>
        <div>
          <div class="text-label-lg font-bold">Semua kasbon sudah lunas</div>
          <div class="text-body-sm text-on-surface-variant">Belanja berikutnya bisa dicatat lewat Kasir &gt; Kasbon.</div>
        </div>
      </div>
    @endif

    <form method="POST" action="{{ route('seller.kasbon.debt', $debtor) }}" class="panel space-y-3 p-5">
      @csrf
      <h2 class="text-headline-sm font-bold">Tambah kasbon manual</h2>
      <div>
        <label class="label" for="debt_amount">Jumlah (Rp)</label>
        <input id="debt_amount" type="number" min="1" name="amount" required inputmode="numeric" class="input">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label" for="debt_date">Tanggal</label>
          <input id="debt_date" type="date" name="date" value="{{ now()->toDateString() }}" required class="input">
        </div>
        <div>
          <label class="label" for="debt_note">Keterangan</label>
          <input id="debt_note" name="note" class="input" placeholder="Opsional">
        </div>
      </div>
      <button class="btn-ghost w-full">Tambah kasbon</button>
    </form>
  </div>

  {{-- Riwayat --}}
  <section>
    <h2 class="mb-2 text-headline-sm font-bold">Riwayat</h2>
    <div class="panel">
      <table class="rtable w-full">
        <thead class="border-b border-outline-variant/40"><tr>
          <th class="th">Tanggal</th><th class="th">Keterangan</th><th class="th">Jenis</th>
          <th class="th text-right">Jumlah</th><th class="th text-right">Sisa</th><th class="th"></th>
        </tr></thead>
        <tbody class="divide-y divide-outline-variant/30">
          @forelse ($entries as $e)
            <tr>
              <td class="td whitespace-nowrap text-on-surface-variant" data-label="Tanggal">{{ $e->date->locale('id')->translatedFormat('d M Y') }}</td>
              <td class="td" data-label="Keterangan">
                <div>
                  {{ $e->note ?: ($e->type === 'payment' ? 'Pembayaran' : 'Kasbon') }}
                  @if ($e->order_id)
                    <a href="{{ route('seller.orders.show', $e->order_id) }}" class="ml-1 text-label-sm text-primary hover:underline">lihat pesanan</a>
                  @endif
                </div>
              </td>
              <td class="td" data-label="Jenis">
                <span class="rounded-full px-2.5 py-0.5 text-label-sm font-bold {{ $e->type === 'payment' ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-secondary-fixed text-on-secondary-fixed' }}">
                  {{ $e->type === 'payment' ? 'Bayar' : 'Kasbon' }}
                </span>
              </td>
              <td class="td whitespace-nowrap text-right font-semibold {{ $e->type === 'payment' ? 'text-primary' : 'text-error' }}" data-label="Jumlah">
                {{ $e->type === 'payment' ? '−' : '+' }} {{ $rp($e->amount) }}
              </td>
              <td class="td whitespace-nowrap text-right text-on-surface-variant" data-label="Sisa">{{ $rp(max(0, $e->running)) }}</td>
              <td class="td rt-actions text-right" data-label="">
                @unless ($e->order_id)
                  <form method="POST" action="{{ route('seller.kasbon.entries.destroy', [$debtor, $e]) }}" onsubmit="return confirm('Hapus catatan ini?')">
                    @csrf @method('DELETE')
                    <button class="text-label-md text-error hover:underline">Hapus</button>
                  </form>
                @endunless
              </td>
            </tr>
          @empty
            <tr><td colspan="6" data-label="" class="rt-empty px-4 py-10 text-center text-body-md text-on-surface-variant">Belum ada catatan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  {{-- Ubah data pelanggan --}}
  <details class="panel p-5">
    <summary class="cursor-pointer text-label-lg font-bold">Ubah data pelanggan</summary>
    <form method="POST" action="{{ route('seller.kasbon.update', $debtor) }}" class="mt-4 grid gap-3 sm:grid-cols-2">
      @csrf @method('PUT')
      <div>
        <label class="label" for="edit_name">Nama</label>
        <input id="edit_name" name="name" value="{{ old('name', $debtor->name) }}" required class="input">
      </div>
      <div>
        <label class="label" for="edit_phone">No. WA</label>
        <input id="edit_phone" name="phone" value="{{ old('phone', $debtor->phone) }}" inputmode="tel" class="input">
      </div>
      <div class="flex gap-2 sm:col-span-2">
        <button class="btn-primary">Simpan</button>
      </div>
    </form>
    @if ($entries->isEmpty())
      <form method="POST" action="{{ route('seller.kasbon.destroy', $debtor) }}" class="mt-3" onsubmit="return confirm('Hapus pelanggan ini?')">
        @csrf @method('DELETE')
        <button class="btn-danger">Hapus pelanggan</button>
      </form>
    @endif
  </details>
</div>
@endsection
