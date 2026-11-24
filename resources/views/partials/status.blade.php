@php
  $map = [
    'pending'   => ['Menunggu', 'bg-secondary-fixed text-on-secondary-fixed'],
    'completed' => ['Selesai', 'bg-primary-fixed text-on-primary-fixed'],
    'cancelled' => ['Dibatalkan', 'bg-surface-container-high text-on-surface-variant'],
  ][$status] ?? [$status, 'bg-surface-container-high text-on-surface-variant'];
@endphp
<span class="inline-block rounded-full px-2.5 py-0.5 text-label-sm font-bold {{ $map[1] }}">{{ $map[0] }}</span>
