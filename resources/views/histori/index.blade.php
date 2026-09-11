@extends('layouts.template_absen')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">

<style>
    *, *::before, *::after { box-sizing: border-box; }
    body {
        background: #F0F2FF;
        font-family: 'Inter', sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* ── PAGE HEADER ── */
    .page-header {
        background: linear-gradient(145deg, #1A1F36 0%, #2D3561 100%);
        padding: clamp(20px, 5vw, 32px) clamp(16px, 5vw, 28px);
        padding-top: calc(clamp(20px, 5vw, 32px) + env(safe-area-inset-top));
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }
    .page-header::before {
        content: '';
        position: absolute;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(67,97,238,0.12);
        top: -50px; right: -40px;
        pointer-events: none;
    }
    .back-btn {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white;
        text-decoration: none;
        font-size: 18px;
        flex-shrink: 0;
        transition: background 0.2s;
    }
    .back-btn:hover { background: rgba(255,255,255,0.2); }
    .page-title {
        font-size: clamp(1rem, 4vw, 1.2rem);
        font-weight: 700;
        color: #fff;
        margin: 0;
        flex: 1;
    }
    .page-header-icon {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,0.7);
        font-size: 18px;
    }

    /* ── MAIN WRAPPER ── */
    #appCapsule {
        max-width: 640px;
        margin: 0 auto;
    }
    @media (min-width: 900px) {
        #appCapsule { max-width: 900px; }
    }

    /* ── FILTER CARD ── */
    .filter-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        margin: 16px clamp(12px, 4vw, 20px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }
    .filter-card .form-control {
        flex: 1;
        border: 1.5px solid #E5E7EB;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.88rem;
        font-family: 'Inter', sans-serif;
        color: #1A1F36;
        outline: none;
        transition: border-color 0.2s;
        background: #F9FAFB;
    }
    .filter-card .form-control:focus { border-color: #4361EE; background: #fff; }
    .filter-card .form-control::placeholder { color: #9CA3AF; }
    .btn-filter {
        background: linear-gradient(135deg, #4361EE, #3A56D4);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 0.85rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        display: flex; align-items: center; gap: 6px;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(67,97,238,0.3);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .btn-filter:active { transform: scale(0.97); }
    .btn-filter ion-icon { font-size: 16px; }

    /* ── SECTION LABEL ── */
    .section-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #6B7280;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 0 clamp(12px, 4vw, 20px);
        margin-bottom: 10px;
    }

    /* ── HISTORY CARDS ── */
    .history-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 0 clamp(12px, 4vw, 20px);
        margin-bottom: 24px;
    }
    @media (min-width: 640px) {
        .history-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
    }
    @media (min-width: 900px) {
        .history-list { grid-template-columns: repeat(3, 1fr); }
    }

    .history-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        min-width: 0;
    }
    .history-card-date {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid #F3F4F6;
    }
    .date-icon {
        width: 32px; height: 32px;
        background: #EEF2FF;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        color: #4361EE;
        font-size: 15px;
        flex-shrink: 0;
    }
    .date-text {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1A1F36;
    }

    .presence-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .presence-col-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: #9CA3AF;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }
    .time-chip {
        display: inline-flex;
        align-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 8px;
    }
    .time-chip.in      { background: #D1FAE5; color: #065F46; }
    .time-chip.out     { background: #FFE4E6; color: #9F1239; }
    .time-chip.pending { background: #F3F4F6; color: #9CA3AF; }

    .presence-photo {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        border-radius: 10px;
        border: 1.5px solid #F3F4F6;
        display: block;
    }
    .photo-empty {
        width: 100%;
        aspect-ratio: 1;
        background: #F9FAFB;
        border-radius: 10px;
        border: 1.5px dashed #E5E7EB;
        display: flex; align-items: center; justify-content: center;
        color: #D1D5DB;
        font-size: 22px;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #9CA3AF;
    }
    .empty-state ion-icon { font-size: 48px; display: block; margin-bottom: 12px; color: #D1D5DB; }
    .empty-state p { font-size: 0.88rem; font-weight: 500; margin: 0; }
</style>

<body>
<div id="appCapsule">

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <a href="javascript:history.back()" class="back-btn">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
        <h1 class="page-title">Histori Absen</h1>
        <div class="page-header-icon">
            <ion-icon name="calendar-outline"></ion-icon>
        </div>
    </div>

    {{-- ── FILTER ── --}}
    <form id="form_filter" method="GET" action="{{ route('absen.histori') }}">
        <div class="filter-card">
            <input type="text" name="periode_start" id="periode_start"
                class="form-control yearmonthpicker"
                placeholder="Pilih Bulan (YYYY-MM)"
                autocomplete="off"
                value="{{ $periode_start ?? '' }}">
            <button type="submit" class="btn-filter">
                <ion-icon name="search-outline"></ion-icon>
                Cari
            </button>
        </div>
    </form>

    {{-- ── RESULTS ── --}}
    @if(isset($absensi) && $absensi->count() > 0)
        <p class="section-label">{{ $absensi->count() }} data ditemukan</p>
        <div class="history-list">
            @foreach($absensi as $item)
            <div class="history-card">
                <div class="history-card-date">
                    <div class="date-icon"><ion-icon name="finger-print-outline"></ion-icon></div>
                    <span class="date-text">{{ date("d M Y", strtotime($item->tgl_absen)) }}</span>
                </div>
                <div class="presence-cols">
                    {{-- Masuk --}}
                    <div>
                        <div class="presence-col-label">MASUK</div>
                        @if($item->jam_masuk)
                            <div class="time-chip in">{{ $item->jam_masuk }}</div>
                        @else
                            <div class="time-chip pending">–</div>
                        @endif
                        @if($item->foto_masuk)
                            @php $pathMasuk = Storage::url('uploads/absensi/' . $item->foto_masuk); @endphp
                            <img src="{{ url($pathMasuk) }}" alt="Foto Masuk" class="presence-photo">
                        @else
                            <div class="photo-empty"><ion-icon name="camera-outline"></ion-icon></div>
                        @endif
                    </div>
                    {{-- Keluar --}}
                    <div>
                        <div class="presence-col-label">KELUAR</div>
                        @if($item->jam_keluar)
                            <div class="time-chip out">{{ $item->jam_keluar }}</div>
                        @else
                            <div class="time-chip pending">Belum</div>
                        @endif
                        @if($item->foto_keluar)
                            @php $pathKeluar = Storage::url('uploads/absensi/' . $item->foto_keluar); @endphp
                            <img src="{{ url($pathKeluar) }}" alt="Foto Keluar" class="presence-photo">
                        @else
                            <div class="photo-empty"><ion-icon name="camera-outline"></ion-icon></div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <ion-icon name="document-outline"></ion-icon>
            <p>Tidak ada data absensi untuk periode ini</p>
        </div>
    @endif

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $('.yearmonthpicker').datepicker({
        format: "yyyy-mm",
        minViewMode: "months",
        startView: "years",
        autoclose: true
    });
</script>
@endpush

@endsection
