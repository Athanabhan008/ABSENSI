@php
    $lat = '';
    $lng = '';
    if ($absensi && !empty($absensi->lokasi_masuk)) {
        $parts = array_map('trim', explode(',', $absensi->lokasi_masuk));
        $lat = $parts[0] ?? '';
        $lng = $parts[1] ?? '';
    }
@endphp
@if ($lat === '' || $lng === '')
    <p class="text-muted mb-0">Lokasi absensi tidak tersedia.</p>
@else
    <div id="mapAbsensi" class="rounded" style="height: 320px; width: 100%; min-height: 280px; z-index: 0;"
         data-lat="{{ e($lat) }}" data-lng="{{ e($lng) }}"></div>
@endif
