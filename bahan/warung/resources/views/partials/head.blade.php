<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
@verbatim
<script>
tailwind.config = { theme: { extend: {
  colors: {
    "primary": "#005f3f", "on-primary": "#ffffff", "primary-container": "#0d7a53", "on-primary-container": "#a6ffd0",
    "primary-fixed": "#98f6c5", "primary-fixed-dim": "#7cd9aa", "on-primary-fixed": "#002113", "on-primary-fixed-variant": "#005236",
    "secondary": "#8a5100", "on-secondary": "#ffffff", "secondary-container": "#fe9d20", "on-secondary-container": "#673b00",
    "secondary-fixed": "#ffdcbd", "secondary-fixed-dim": "#ffb86e", "on-secondary-fixed": "#2c1600", "on-secondary-fixed-variant": "#693c00",
    "tertiary": "#344d9e", "tertiary-container": "#4e66b8", "tertiary-fixed": "#dce1ff", "on-tertiary-fixed": "#00164e",
    "error": "#ba1a1a", "on-error": "#ffffff", "error-container": "#ffdad6", "on-error-container": "#93000a",
    "background": "#f6faf7", "on-background": "#181d1b", "surface": "#f6faf7", "on-surface": "#181d1b",
    "surface-variant": "#dfe4e0", "on-surface-variant": "#3e4942", "surface-tint": "#006c48",
    "surface-container-lowest": "#ffffff", "surface-container-low": "#f0f5f1", "surface-container": "#eaefeb",
    "surface-container-high": "#e5e9e6", "surface-container-highest": "#dfe4e0", "surface-dim": "#d6dbd8", "surface-bright": "#f6faf7",
    "outline": "#6e7a72", "outline-variant": "#bec9c0", "inverse-surface": "#2c312f", "inverse-on-surface": "#edf2ee", "inverse-primary": "#7cd9aa"
  },
  borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
  spacing: {
    "space-xs": "0.25rem", "space-sm": "0.5rem", "space-md": "1rem", "space-lg": "1.5rem", "space-xl": "2rem", "space-2xl": "3rem"
  },
  fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
  fontSize: {
    "label-sm": ["0.6875rem", { lineHeight: "0.875rem", letterSpacing: "0.04em", fontWeight: "700" }],
    "label-md": ["0.75rem", { lineHeight: "1rem", letterSpacing: "0.02em", fontWeight: "600" }],
    "label-lg": ["0.875rem", { lineHeight: "1.25rem", letterSpacing: "0.01em", fontWeight: "600" }],
    "body-sm": ["0.75rem", { lineHeight: "1.125rem", fontWeight: "400" }],
    "body-md": ["0.875rem", { lineHeight: "1.375rem", fontWeight: "400" }],
    "body-lg": ["1rem", { lineHeight: "1.5rem", fontWeight: "400" }],
    "headline-sm": ["1.125rem", { lineHeight: "1.5rem", fontWeight: "600" }],
    "headline-md": ["1.25rem", { lineHeight: "1.75rem", letterSpacing: "-0.01em", fontWeight: "600" }],
    "headline-lg-mobile": ["1.5rem", { lineHeight: "2rem", letterSpacing: "-0.01em", fontWeight: "700" }],
    "headline-lg": ["2rem", { lineHeight: "2.5rem", letterSpacing: "-0.02em", fontWeight: "700" }],
    "currency-stat": ["1.75rem", { lineHeight: "2rem", letterSpacing: "-0.02em", fontWeight: "800" }],
    "display-mobile": ["2.25rem", { lineHeight: "2.75rem", letterSpacing: "-0.02em", fontWeight: "800" }],
    "display": ["3rem", { lineHeight: "3.5rem", letterSpacing: "-0.03em", fontWeight: "800" }]
  }
} } }
</script>
<style type="text/tailwindcss">
  @layer components {
    .btn { @apply inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-label-lg transition active:scale-[0.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:cursor-not-allowed disabled:opacity-40; }
    .btn-primary { @apply btn bg-primary text-on-primary shadow-sm hover:bg-primary-container; }
    .btn-ghost { @apply btn bg-surface-container-low text-on-surface shadow-sm hover:bg-surface-container-high; }
    .btn-danger { @apply btn bg-error-container text-error hover:bg-error-container/70; }
    .input { @apply w-full rounded-xl bg-surface-container-low px-3 py-2.5 text-body-md text-on-surface placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary/30; }
    .label { @apply mb-1 block text-label-lg text-on-surface; }
    .panel { @apply rounded-2xl bg-surface-container-lowest shadow-sm; }
    .th { @apply px-4 py-3 text-left text-label-sm uppercase text-on-surface-variant; }
    .td { @apply px-4 py-3 text-body-md; }
  }
  [x-cloak] { display: none !important; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; line-height: 1; user-select: none; }
  @media (max-width: 767px) {
    .rtable thead { display: none; }
    .rtable, .rtable tbody, .rtable tfoot, .rtable tr { display: block; width: 100%; }
    .rtable tr { padding: .625rem 1rem; }
    .rtable td { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .3rem 0 !important; text-align: right; }
    .rtable td::before { content: attr(data-label); flex-shrink: 0; text-align: left; font-size: .75rem; font-weight: 600; color: #3e4942; }
    .rtable td[data-label=""]::before { content: none; }
    .rtable td.rt-title { justify-content: flex-start; text-align: left; font-weight: 600; }
    .rtable td.rt-actions { justify-content: flex-end; }
    .rtable td.rt-empty { display: block; text-align: center; padding: 2rem 0 !important; }
  }
</style>
@endverbatim
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
