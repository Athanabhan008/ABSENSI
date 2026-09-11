@extends('layouts.template_absen')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    *, *::before, *::after { box-sizing: border-box; }

    body {
        background: #F0F2FF;
        font-family: 'Inter', sans-serif;
        margin: 0; padding: 0;
        -webkit-font-smoothing: antialiased;
    }

    /* ── LOADER ── */
    #loader {
        position: fixed; inset: 0;
        background: #1A1F36;
        display: flex; align-items: center; justify-content: center;
        z-index: 9999;
        transition: opacity 0.3s ease;
    }
    #loader.hidden {
        opacity: 0;
        pointer-events: none;
    }
    /* Self-contained spinner — tidak bergantung Bootstrap */
    #loader .spin {
        width: 36px; height: 36px;
        border: 3px solid rgba(67, 97, 238, 0.2);
        border-top-color: #4361EE;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ── APP WRAPPER ──
       Mobile:  full width
       Tablet+: centered card feel, max 640px
       Desktop: wider, max 900px, two-column layout
    ── */
    #appCapsule {
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
        min-height: 100vh;
        overflow-x: hidden;
    }
    @media (min-width: 900px) {
        #appCapsule {
            max-width: 900px;
        }
        /* desktop: hero + presence side, content right */
        .desktop-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 0;
            align-items: start;
        }
        .desktop-left {
            position: sticky;
            top: 0;
        }
        .desktop-right {
            padding: 28px 28px 100px;
            min-height: 100vh;
        }
    }
    @media (max-width: 899px) {
        .desktop-layout { display: block; }
        .desktop-left {}
        .desktop-right { padding: 0; }
    }

    /* ── HERO HEADER ── */
    #user-section {
        background: linear-gradient(145deg, #1A1F36 0%, #2D3561 100%);
        padding: clamp(36px, 8vw, 64px) clamp(16px, 5vw, 32px) clamp(56px, 10vw, 80px);
        position: relative;
        overflow: hidden;
    }
    @media (min-width: 900px) {
        #user-section {
            min-height: 100vh;
            padding: 48px 32px 80px;
            border-radius: 0;
        }
    }
    #user-section::before {
        content: '';
        position: absolute;
        width: clamp(150px, 40vw, 240px);
        height: clamp(150px, 40vw, 240px);
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.15);
        top: -60px; right: -60px;
        pointer-events: none;
    }
    #user-section::after {
        content: '';
        position: absolute;
        width: clamp(80px, 20vw, 140px);
        height: clamp(80px, 20vw, 140px);
        border-radius: 50%;
        background: rgba(6, 214, 160, 0.1);
        bottom: 20px; left: -30px;
        pointer-events: none;
    }

    .logout-btn {
        position: absolute;
        top: 18px; right: 18px;
        width: 38px; height: 38px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white;
        text-decoration: none;
        font-size: 20px;
        transition: background 0.2s;
        z-index: 10;
    }
    .logout-btn:hover { background: rgba(255,255,255,0.2); }

    .user-avatar {
        width: clamp(48px, 12vw, 72px);
        height: clamp(48px, 12vw, 72px);
        border-radius: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        object-fit: cover;
        margin-bottom: 12px;
    }
    .user-greeting {
        font-size: clamp(0.68rem, 2vw, 0.8rem);
        color: rgba(255,255,255,0.55);
        font-weight: 500;
        letter-spacing: 0.03em;
        margin: 0 0 2px;
    }
    .user-name {
        font-size: clamp(1.1rem, 4vw, 1.5rem);
        font-weight: 700;
        color: #fff;
        margin: 0 0 8px;
        line-height: 1.2;
        /* truncate long names on tiny screens */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: calc(100% - 60px);
    }
    .user-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(67, 97, 238, 0.35);
        color: #A5B4FC;
        font-size: clamp(0.65rem, 2vw, 0.75rem);
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .user-role-badge ion-icon { font-size: 12px; }

    /* ── PRESENCE CARDS ──
       Mobile:  floating overlap on hero (-44px margin)
       Desktop: inline below hero info in left column
    ── */
    .presence-float {
        margin: -44px 16px 0;
        position: relative;
        z-index: 5;
    }
    @media (min-width: 900px) {
        .presence-float {
            margin: 32px 32px 0;
        }
    }
    @media (max-width: 360px) {
        .presence-float { margin: -36px 12px 0; }
    }

    .presence-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .presence-card {
        border-radius: 16px;
        padding: clamp(10px, 3vw, 16px);
        display: flex;
        flex-direction: column;
        gap: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .presence-card.masuk  { background: linear-gradient(135deg, #06D6A0, #059669); }
    .presence-card.pulang { background: linear-gradient(135deg, #EF476F, #C53030); }

    .presence-label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        color: rgba(255,255,255,0.75);
        text-transform: uppercase;
    }
    .presence-photo {
        width: clamp(36px, 10vw, 48px);
        height: clamp(36px, 10vw, 48px);
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(255,255,255,0.4);
        flex-shrink: 0;
    }
    .presence-icon-empty {
        width: clamp(36px, 10vw, 48px);
        height: clamp(36px, 10vw, 48px);
        border-radius: 10px;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        color: rgba(255,255,255,0.6);
        flex-shrink: 0;
    }
    .presence-time {
        font-size: clamp(0.85rem, 3vw, 1.05rem);
        font-weight: 700;
        color: #fff;
        line-height: 1;
        word-break: break-all;
    }
    .presence-time.empty {
        font-size: clamp(0.65rem, 2vw, 0.75rem);
        font-weight: 500;
        color: rgba(255,255,255,0.7);
    }
    .presence-row-inner {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* ── MAIN CONTENT ── */
    .main-content {
        padding: clamp(16px, 4vw, 24px) clamp(12px, 4vw, 20px) 100px;
    }
    @media (min-width: 900px) {
        .main-content { padding: 0; } /* desktop-right handles padding */
    }

    /* ── MONTH HEADER ── */
    .month-header {
        background: #fff;
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        margin-top: clamp(16px, 4vw, 24px);
    }
    .month-title {
        font-size: clamp(0.82rem, 2.5vw, 0.95rem);
        font-weight: 700;
        color: #1A1F36;
    }
    .month-sub {
        font-size: 0.7rem;
        color: #9CA3AF;
        font-weight: 500;
        margin-top: 1px;
    }
    .month-icon {
        width: 34px; height: 34px;
        background: #EEF2FF;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #4361EE;
        font-size: 17px;
        flex-shrink: 0;
    }

    /* ── REKAP GRID ──
       Mobile:  2 kolom
       Tablet:  2 kolom lebar
       Desktop: 4 kolom (inside right panel)
    ── */
    .rekap-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 24px;
    }
    @media (min-width: 600px) and (max-width: 899px) {
        .rekap-grid { grid-template-columns: repeat(4, 1fr); }
    }
    @media (min-width: 900px) {
        .rekap-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .rekap-card {
        background: #fff;
        border-radius: 14px;
        padding: clamp(10px, 3vw, 16px) clamp(8px, 2vw, 14px);
        display: flex;
        align-items: center;
        gap: clamp(8px, 2vw, 14px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        min-width: 0; /* prevent overflow */
    }
    .rekap-icon {
        width: clamp(34px, 8vw, 44px);
        height: clamp(34px, 8vw, 44px);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: clamp(17px, 4vw, 22px);
        flex-shrink: 0;
    }
    .rekap-icon.hadir { background: #EEF2FF; color: #4361EE; }
    .rekap-icon.izin  { background: #F0FDF4; color: #16A34A; }
    .rekap-icon.sakit { background: #FFFBEB; color: #D97706; }
    .rekap-icon.telat { background: #FFF1F2; color: #EF476F; }

    .rekap-count {
        font-size: clamp(1.2rem, 4vw, 1.5rem);
        font-weight: 800;
        color: #1A1F36;
        line-height: 1;
        margin-bottom: 2px;
    }
    .rekap-desc {
        font-size: clamp(0.62rem, 1.5vw, 0.72rem);
        font-weight: 600;
        color: #9CA3AF;
        letter-spacing: 0.04em;
    }

    /* ── TABS ── */
    .tab-header {
        display: flex;
        background: #E5E7EB;
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 16px;
    }
    .tab-btn {
        flex: 1;
        text-align: center;
        padding: 8px;
        font-size: clamp(0.78rem, 2.5vw, 0.88rem);
        font-weight: 600;
        color: #6B7280;
        border-radius: 9px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: block;
        white-space: nowrap;
    }
    .tab-btn.active {
        background: #fff;
        color: #1A1F36;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }

    /* ── HISTORY LIST ── */
    .history-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .history-item {
        background: #fff;
        border-radius: 14px;
        padding: clamp(10px, 3vw, 14px) clamp(10px, 3vw, 16px);
        display: flex;
        align-items: center;
        gap: clamp(8px, 2vw, 14px);
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        min-width: 0;
    }
    .history-icon {
        width: 36px; height: 36px;
        background: #EEF2FF;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #4361EE;
        font-size: 17px;
        flex-shrink: 0;
    }
    .history-date {
        font-size: clamp(0.75rem, 2.5vw, 0.85rem);
        font-weight: 600;
        color: #1A1F36;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .badges {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .time-badge {
        font-size: clamp(0.6rem, 1.8vw, 0.7rem);
        font-weight: 600;
        padding: 3px 7px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .time-badge.in      { background: #D1FAE5; color: #065F46; }
    .time-badge.out     { background: #FFE4E6; color: #9F1239; }
    .time-badge.pending { background: #F3F4F6; color: #9CA3AF; }

    /* ── LEADERBOARD ── */
    .lb-item {
        background: #fff;
        border-radius: 14px;
        padding: clamp(10px, 3vw, 14px) clamp(10px, 3vw, 16px);
        display: flex;
        align-items: center;
        gap: clamp(8px, 2vw, 14px);
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        min-width: 0;
    }
    .lb-rank {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: #EEF2FF;
        color: #4361EE;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .lb-rank.gold   { background: #FFFBEB; color: #B45309; }
    .lb-rank.silver { background: #F3F4F6; color: #4B5563; }
    .lb-rank.bronze { background: #FFF7ED; color: #92400E; }
    .lb-avatar {
        width: 36px; height: 36px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
    }
    .lb-name {
        font-size: clamp(0.78rem, 2.5vw, 0.88rem);
        font-weight: 600;
        color: #1A1F36;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .lb-time {
        font-size: clamp(0.65rem, 2vw, 0.75rem);
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .lb-time.ontime { background: #D1FAE5; color: #065F46; }
    .lb-time.late   { background: #FFE4E6; color: #9F1239; }

    /* tab content */
    .tab-content-panel { display: none; }
    .tab-content-panel.active { display: block; }

    /* ── TINY SCREEN (<360px) tweaks ── */
    @media (max-width: 359px) {
        .presence-card { padding: 10px; gap: 8px; }
        .rekap-card    { padding: 10px 8px; gap: 8px; }
        .history-item, .lb-item { padding: 10px; }
    }

    /* Menyelaraskan gambar dan teks berdampingan */
.user-profile-info {
    display: flex;
    align-items: center; /* Menyejajarkan posisi secara vertikal di tengah */
    gap: 16px;          /* Jarak antara gambar avatar dan konten teks */
}

/* Memastikan konten teks menyesuaikan sisa ruang */
.user-details {
    flex: 1;
    min-width: 0; /* Menjaga efek ellipsis/truncation pada .user-name tetap berfungsi */
}

/* Menghapus max-width terbatas dari kode lama jika ada, agar teks fleksibel */
.user-name {
    max-width: 100%;
}
</style>

<body>

    <div id="loader">
        <div class="spin"></div>
    </div>

    <div id="appCapsule">
        <div class="desktop-layout">

            {{-- ── LEFT COLUMN (hero + presence) ── --}}
            <div class="desktop-left">

                <div id="user-section">
                    <a href="/logout" class="logout-btn" title="Logout">
                        <ion-icon name="log-out-outline"></ion-icon>
                    </a>

                    <!-- Pembungkus Flexbox Utama -->
                    <div class="user-profile-info">
                        <img src="{{ asset('absensi/assets/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="user-avatar">

                        <!-- Pembungkus Teks -->
                        <div class="user-details">
                            <p class="user-greeting">Selamat datang,</p>
                            <h2 class="user-name">{{ auth()->user()->name }}</h2>
                            <span class="user-role-badge">
                                <ion-icon name="briefcase-outline"></ion-icon>
                                Head of IT
                            </span>
                        </div>
                    </div>
                </div>

                <div class="presence-float">
                    <div class="presence-row">
                        <div class="presence-card masuk">
                            <span class="presence-label">Masuk</span>
                            <div class="presence-row-inner">
                                @if ($absensihariini != null)
                                    @php $path = Storage::url('uploads/absensi/' . $absensihariini->foto_masuk); @endphp
                                    <img src="{{ url($path) }}" class="presence-photo" alt="foto masuk">
                                @else
                                    <div class="presence-icon-empty"><ion-icon name="camera-outline"></ion-icon></div>
                                @endif
                                <span class="{{ $absensihariini != null ? 'presence-time' : 'presence-time empty' }}">
                                    {{ $absensihariini != null ? $absensihariini->jam_masuk : 'Belum absen' }}
                                </span>
                            </div>
                        </div>

                        <div class="presence-card pulang">
                            <span class="presence-label">Pulang</span>
                            <div class="presence-row-inner">
                                @if ($absensihariini != null && $absensihariini->foto_keluar != null)
                                    @php $path = Storage::url('uploads/absensi/' . $absensihariini->foto_keluar); @endphp
                                    <img src="{{ url($path) }}" class="presence-photo" alt="foto keluar">
                                @else
                                    <div class="presence-icon-empty"><ion-icon name="camera-outline"></ion-icon></div>
                                @endif
                                <span class="{{ ($absensihariini != null && $absensihariini->jam_keluar != null) ? 'presence-time' : 'presence-time empty' }}">
                                    {{ ($absensihariini != null && $absensihariini->jam_keluar != null) ? $absensihariini->jam_keluar : 'Belum absen' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ── END LEFT COLUMN ── --}}

            {{-- ── RIGHT COLUMN (main content) ── --}}
            <div class="desktop-right">
                <div class="main-content">

                    <div class="month-header">
                        <div>
                            <div class="month-title">{{ $bulan_nama_carbon }} {{ $tahunSekarang }}</div>
                            <div class="month-sub">Rekap kehadiran bulan ini</div>
                        </div>
                        <div class="month-icon"><ion-icon name="calendar-outline"></ion-icon></div>
                    </div>

                    <div class="rekap-grid">
                        <div class="rekap-card">
                            <div class="rekap-icon hadir"><ion-icon name="accessibility-outline"></ion-icon></div>
                            <div class="rekap-info">
                                <div class="rekap-count">{{ $rekappresensi->jmlhadir }}</div>
                                <div class="rekap-desc">Hadir</div>
                            </div>
                        </div>
                        <div class="rekap-card">
                            <div class="rekap-icon izin"><ion-icon name="newspaper-outline"></ion-icon></div>
                            <div class="rekap-info">
                                <div class="rekap-count">{{ $jmlcuti ?? 0 }}</div>
                                <div class="rekap-desc">Izin</div>
                            </div>
                        </div>
                        <div class="rekap-card">
                            @php $jmlsakit = $rekapsakit->first()->jmlsakit ?? 0; @endphp
                            <div class="rekap-icon sakit"><ion-icon name="medkit-outline"></ion-icon></div>
                            <div class="rekap-info">
                                <div class="rekap-count">{{ $jmlsakit }}</div>
                                <div class="rekap-desc">Sakit</div>
                            </div>
                        </div>
                        <div class="rekap-card">
                            <div class="rekap-icon telat"><ion-icon name="alarm-outline"></ion-icon></div>
                            <div class="rekap-info">
                                <div class="rekap-count">{{ $rekappresensi->jmlterlambat }}</div>
                                <div class="rekap-desc">Telat</div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-header">
                        <a class="tab-btn active" onclick="switchTab('bulanini', this)">Bulan Ini</a>
                        <a class="tab-btn" onclick="switchTab('leaderboard', this)">Leaderboard</a>
                    </div>

                    <div id="bulanini" class="tab-content-panel active">
                        <div class="history-list">
                            @foreach ($historibulanini as $item)
                            <div class="history-item">
                                <div class="history-icon"><ion-icon name="finger-print-outline"></ion-icon></div>
                                <span class="history-date">{{ date("d M Y", strtotime($item->tgl_absen)) }}</span>
                                <div class="badges">
                                    <span class="time-badge in">{{ $item->jam_masuk }}</span>
                                    @if($item->jam_keluar != null)
                                        <span class="time-badge out">{{ $item->jam_keluar }}</span>
                                    @else
                                        <span class="time-badge pending">–</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="leaderboard" class="tab-content-panel">
                        <div class="history-list">
                            @foreach ($leaderboard as $index => $lb)
                            <div class="lb-item">
                                @php
                                    $rankClass = $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : ''));
                                @endphp
                                <div class="lb-rank {{ $rankClass }}">{{ $index + 1 }}</div>
                                <img src="{{ asset('absensi/assets/img/sample/avatar/avatar1.jpg') }}" class="lb-avatar" alt="">
                                <span class="lb-name">{{ $lb->name }}</span>
                                <span class="lb-time {{ $lb->jam_masuk < '08:15' ? 'ontime' : 'late' }}">{{ $lb->jam_masuk }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
            {{-- ── END RIGHT COLUMN ── --}}

        </div>{{-- .desktop-layout --}}
    </div>

    <script>
        function switchTab(tabId, el) {
            document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            el.classList.add('active');
        }

        function hideLoader() {
            var l = document.getElementById('loader');
            if (!l) return;
            l.classList.add('hidden');
            // remove from DOM after fade
            setTimeout(function() { if (l.parentNode) l.parentNode.removeChild(l); }, 350);
        }

        // Strategy 1: DOMContentLoaded (earliest possible)
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(hideLoader, 300);
        });

        // Strategy 2: window.load (all resources done)
        window.addEventListener('load', function() {
            hideLoader();
        });

        // Strategy 3: hard timeout fallback — selalu hilang max 3 detik
        setTimeout(hideLoader, 3000);
    </script>

@endsection
