@if (session('success'))
  <div class="mb-4 flex items-start gap-2 rounded-xl bg-primary-fixed px-4 py-3 text-label-lg text-on-primary-fixed">
    <span class="material-symbols-outlined text-[20px]">check_circle</span>
    <span>{{ session('success') }}</span>
  </div>
@endif
@if ($errors->any())
  <div class="mb-4 flex items-start gap-2 rounded-xl bg-error-container px-4 py-3 text-label-lg text-on-error-container">
    <span class="material-symbols-outlined text-[20px]">error</span>
    <div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
  </div>
@endif
