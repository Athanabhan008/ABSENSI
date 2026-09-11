<!doctype html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1A1F36">
    <title>Absensi</title>
    <meta name="description" content="Sistem Absensi Karyawan">
    <link rel="icon" type="image/png" href="{{ asset('absensi/assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('absensi/assets/img/icon/192x192.png') }}">
    <link rel="stylesheet" href="{{ asset('absensi/assets/css/style.css') }}">
    <link rel="manifest" href="{{ asset('_manifest.json') }}_">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/90c4b6e831.js" crossorigin="anonymous"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #F0F2FF;
            margin: 0; padding: 0;
            -webkit-font-smoothing: antialiased;
            /* ruang untuk bottom nav mobile */
            padding-bottom: 80px;
        }
        @media (min-width: 900px) {
            body { padding-bottom: 0; }
        }

        /* ── BOTTOM NAV (mobile & tablet) ── */
        .bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 10px 8px calc(10px + env(safe-area-inset-bottom));
            box-shadow: 0 -1px 0 #E5E7EB, 0 -4px 20px rgba(0,0,0,0.06);
            z-index: 1000;
            border-radius: 20px 20px 0 0;
        }
        /* Tablet: constrain width to match appCapsule */
        @media (min-width: 600px) and (max-width: 899px) {
            .bottom-nav {
                max-width: 640px;
                left: 50%; right: auto;
                transform: translateX(-50%);
                border-radius: 20px 20px 0 0;
            }
        }
        /* Desktop: convert to left sidebar */
        @media (min-width: 900px) {
            .bottom-nav {
                position: fixed;
                bottom: auto;
                top: 0; left: 0;
                width: 72px;
                height: 100vh;
                flex-direction: column;
                justify-content: flex-start;
                padding: 24px 8px;
                gap: 4px;
                border-radius: 0;
                box-shadow: 1px 0 0 #E5E7EB, 4px 0 20px rgba(0,0,0,0.04);
                /* push appCapsule right — handled by body margin */
            }
            body {
                padding-left: 72px;
                padding-bottom: 0;
            }
            /* spacer so first nav item doesn't sit at very top */
            .bottom-nav::before {
                content: '';
                display: block;
                height: 20px;
            }
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            text-decoration: none;
            color: #9CA3AF;
            font-size: 0.58rem;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 12px;
            transition: color 0.2s, background 0.2s;
            position: relative;
            min-width: 52px;
        }
        .nav-item ion-icon { font-size: 22px; display: block; }

        .nav-item.active { color: #4361EE; }
        .nav-item.active ion-icon { color: #4361EE; }

        /* dot indicator – mobile only */
        @media (max-width: 899px) {
            .nav-item.active::after {
                content: '';
                position: absolute;
                bottom: 2px;
                left: 50%; transform: translateX(-50%);
                width: 4px; height: 4px;
                background: #4361EE;
                border-radius: 50%;
            }
        }
        /* active pill – desktop sidebar */
        @media (min-width: 900px) {
            .nav-item {
                width: 52px;
                min-width: unset;
                border-radius: 14px;
                padding: 10px 6px;
            }
            .nav-item.active {
                background: #EEF2FF;
            }
            .nav-item span { font-size: 0.55rem; }
        }

        /* ── CENTER ABSEN BUTTON ── */
        .nav-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            text-decoration: none;
        }
        .nav-center-btn {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #4361EE, #3A56D4);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 14px rgba(67, 97, 238, 0.4);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .nav-center-btn:active {
            transform: scale(0.95);
            box-shadow: 0 2px 8px rgba(67, 97, 238, 0.3);
        }
        @media (max-width: 899px) {
            .nav-center-btn { margin-top: -20px; }
        }
        @media (min-width: 900px) {
            .nav-center { margin: 8px 0; }
            .nav-center-btn { width: 46px; height: 46px; font-size: 20px; }
        }
        .nav-center-label {
            font-size: 0.58rem;
            font-weight: 600;
            color: #4361EE;
            margin-top: 2px;
        }

        /* hide original Bootstrap bottom menu if present */
        .appBottomMenu { display: none !important; }

        /* ── SHARED: appHeader override ── */
        .appHeader {
            background: linear-gradient(145deg, #1A1F36 0%, #2D3561 100%) !important;
            border: none !important;
            box-shadow: none !important;
        }
        .appHeader .pageTitle {
            font-family: 'Inter', sans-serif !important;
            font-weight: 700 !important;
        }
        .appHeader .headerButton { color: white !important; }
    </style>
</head>

@yield('content')

<!-- ── BOTTOM NAV ── -->
<nav class="bottom-nav">
    <a href="/dashboard" class="nav-item {{ ($active ?? '') === 'manager' ? 'active' : '' }}">
        <ion-icon name="{{ ($active ?? '') === 'manager' ? 'home' : 'home-outline' }}"></ion-icon>
        <span>Home</span>
    </a>
    <a href="/histori" class="nav-item {{ ($active ?? '') === 'histori' ? 'active' : '' }}">
        <ion-icon name="{{ ($active ?? '') === 'histori' ? 'calendar' : 'calendar-outline' }}"></ion-icon>
        <span>Histori</span>
    </a>
    <a href="/absen" class="nav-center">
        <div class="nav-center-btn">
            <ion-icon name="camera"></ion-icon>
        </div>
        <span class="nav-center-label">Absen</span>
    </a>
    <a href="/izin_sakit" class="nav-item {{ ($active ?? '') === 'izin' ? 'active' : '' }}">
        <ion-icon name="{{ ($active ?? '') === 'izin' ? 'document-text' : 'document-text-outline' }}"></ion-icon>
        <span>Izin/Sakit</span>
    </a>
    <a href="/profile" class="nav-item {{ ($active ?? '') === 'profile' ? 'active' : '' }}">
        <ion-icon name="{{ ($active ?? '') === 'profile' ? 'person' : 'person-outline' }}"></ion-icon>
        <span>Profil</span>
    </a>
</nav>

@stack('scripts')

<!-- ── JS ── -->
<script src="{{ asset('absensi/assets/js/lib/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('absensi/assets/js/lib/popper.min.js') }}"></script>
<script src="{{ asset('absensi/assets/js/lib/bootstrap.min.js') }}"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ asset('absensi/assets/js/plugins/owl-carousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('absensi/assets/js/plugins/jquery-circle-progress/circle-progress.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('absensi/assets/js/base.js') }}"></script>

@stack('chart_scripts')
@stack('script')

</body>
</html>
