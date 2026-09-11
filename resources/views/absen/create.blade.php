@extends('layouts.template_absen')

@section('content')

<style>

    *, *::before, *::after { box-sizing: border-box; }

    body{
        background: #F0F2FF;
        font-family: 'Inter', sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* ── LOADER (sama seperti dashboard) ── */
    #loader {
        position: fixed; inset: 0;
        background: #1A1F36;
        display: flex; align-items: center; justify-content: center;
        z-index: 9999;
        transition: opacity 0.3s ease;
    }
    #loader.hidden { opacity: 0; pointer-events: none; }
    #loader .spin {
        width: 36px; height: 36px;
        border: 3px solid rgba(67, 97, 238, 0.2);
        border-top-color: #4361EE;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── APP WRAPPER ── */
    #appCapsule{
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
        min-height: 100vh;
        overflow-x: hidden;
    }
    @media (min-width: 900px){
        #appCapsule{ max-width: 900px; }
        .desktop-layout{
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 0;
            align-items: start;
        }
        .desktop-left{ position: sticky; top: 0; }
        .desktop-right{ padding: 28px 28px 100px; min-height: 100vh; }
    }
    @media (max-width: 899px){
        .desktop-layout{ display: block; }
        .desktop-right{ padding: 0; }
    }

    /* ── HERO HEADER ── */
    #user-section{
        background: linear-gradient(145deg, #1A1F36 0%, #2D3561 100%);
        padding: clamp(28px, 7vw, 48px) clamp(16px, 5vw, 32px) clamp(52px, 9vw, 72px);
        position: relative;
        overflow: hidden;
    }
    #user-section::before{
        content:'';
        position:absolute;
        width: clamp(150px, 40vw, 240px);
        height: clamp(150px, 40vw, 240px);
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.15);
        top: -60px; right: -60px;
        pointer-events: none;
    }
    #user-section::after{
        content:'';
        position:absolute;
        width: clamp(80px, 20vw, 140px);
        height: clamp(80px, 20vw, 140px);
        border-radius: 50%;
        background: rgba(6, 214, 160, 0.1);
        bottom: 20px; left: -30px;
        pointer-events: none;
    }

    .back-btn{
        position:absolute;
        top: 18px; left: 18px;
        width: 38px; height: 38px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        display:flex; align-items:center; justify-content:center;
        color:#fff;
        text-decoration:none;
        font-size: 20px;
        transition: background 0.2s;
        z-index: 10;
    }
    .back-btn:hover{ background: rgba(255,255,255,0.2); }

    .hero-body{ margin-left: 0; }
    .user-greeting{
        font-size: clamp(0.68rem, 2vw, 0.8rem);
        color: rgba(255,255,255,.55);
        font-weight: 500;
        letter-spacing: .03em;
        margin: 30px 0 4px;
        text-align: center;
    }
    .jam-besar{
        font-size: clamp(1.8rem, 7vw, 2.6rem);
        font-weight: 800;
        color: #fff;
        margin: 0 0 10px;
        text-align: center;
        letter-spacing: .02em;
        font-variant-numeric: tabular-nums;
    }
    .status-badge{
        display: flex;
        width: fit-content;
        margin: 0 auto;
        align-items: center;
        gap: 6px;
        font-size: clamp(0.7rem, 2vw, 0.8rem);
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 20px;
        color: #fff;
    }
    .status-badge.masuk{ background: rgba(6, 214, 160, 0.25); color:#6EE7B7; }
    .status-badge.pulang{ background: rgba(239, 71, 111, 0.25); color:#FCA5B1; }
    .status-badge ion-icon{ font-size: 14px; }

    /* ── WEBCAM CARD (gaya presence-float / presence-card) ── */
    .presence-float{
        margin: -32px 16px 0;
        position: relative;
        z-index: 5;
    }
    @media (min-width: 900px){ .presence-float{ margin: 24px 32px 0; } }

    .webcam-card{
        background:#fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 26px rgba(0,0,0,.14);
    }
    .webcam-card-header{
        padding: 12px 14px;
        display:flex; align-items:center; gap:8px;
        font-size: .8rem;
        font-weight: 700;
        color:#1A1F36;
        border-bottom: 1px solid rgba(0,0,0,.06);
        background: linear-gradient(135deg, rgba(67,97,238,.08), rgba(67,97,238,.02));
    }
    .webcam-card-header ion-icon{ color:#4361EE; font-size: 17px; }

    .webcam-frame{
        background: #0b1220;
        aspect-ratio: 4 / 3;
        width: 100%;
    }
    .webcam-frame .webcam-capture{ width:100%; height:100%; }
    .webcam-capture, .webcam-capture video{
        display:block;
        width:100% !important;
        height:auto !important;
    }
    .webcam-capture video{ transform: scaleX(-1) !important; } /* mirror, seperti kamera selfie */

    /* ── MAIN CONTENT ── */
    .main-content{
        padding: clamp(16px, 4vw, 24px) clamp(12px, 4vw, 20px) 100px;
    }
    @media (min-width: 900px){ .main-content{ padding: 0; } }

    .month-header{
        background:#fff;
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 14px;
        display:flex; align-items:center; justify-content:space-between;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        margin-top: clamp(16px, 4vw, 24px);
    }
    .month-title{ font-size: clamp(0.82rem, 2.5vw, 0.95rem); font-weight:700; color:#1A1F36; }
    .month-sub{ font-size:.7rem; color:#9CA3AF; font-weight:500; margin-top:1px; }
    .month-icon{
        width:34px; height:34px;
        background:#EEF2FF;
        border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        color:#4361EE; font-size:17px; flex-shrink:0;
    }

    /* ── MAP CARD ── */
    .map-card{
        background:#fff;
        border-radius: 16px;
        padding: 6px;
        margin-bottom: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    #map{
        width:100%;
        height: 220px;
        border-radius: 12px;
        overflow:hidden;
        position:relative;
    }
    @media (max-width: 575.98px){ #map{ height: 190px; } }

    .refresh-btn{
        position:absolute; top:10px; right:10px; z-index:999;
        border:none;
        background:#4361EE;
        color:#fff;
        font-size:.75rem;
        font-weight:600;
        border-radius: 10px;
        padding: 7px 12px;
        box-shadow: 0 4px 12px rgba(67,97,238,.35);
        cursor:pointer;
    }
    .refresh-btn:disabled{ opacity:.6; cursor: not-allowed; }

    /* ── KETERANGAN CARD ── */
    .keterangan-card{
        background:#fff;
        border-radius: 16px;
        padding: 14px;
        margin-bottom: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    .keterangan-label{
        display:block;
        font-size:.75rem;
        font-weight:700;
        color:#1A1F36;
        margin-bottom: 8px;
    }
    .absen-textarea{
        width:100%;
        min-height: 90px;
        resize: vertical;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,.12);
        padding: 12px;
        line-height: 1.4;
        font-family: 'Inter', sans-serif;
        font-size: .85rem;
        color:#1A1F36;
        transition: border-color .2s, box-shadow .2s;
    }
    .absen-textarea:focus{
        outline: none;
        border-color: rgba(67,97,238,.55);
        box-shadow: 0 0 0 .2rem rgba(67,97,238,.15);
    }

    /* ── TOMBOL ABSEN ── */
    .btn-absen{
        width:100%;
        border:none;
        color:#fff;
        font-size: 1rem;
        font-weight:700;
        padding: 15px;
        border-radius: 14px;
        display:flex; align-items:center; justify-content:center; gap:8px;
        cursor:pointer;
        transition: transform .15s, box-shadow .15s;
    }
    .btn-absen:active{ transform: scale(.98); }
    .btn-absen.masuk{
        background: linear-gradient(135deg, #4361EE, #3A56D4);
        box-shadow: 0 6px 18px rgba(67,97,238,.35);
    }
    .btn-absen.pulang{
        background: linear-gradient(135deg, #EF476F, #C53030);
        box-shadow: 0 6px 18px rgba(239,71,111,.35);
    }

    @media (max-width: 359px){
        .keterangan-card, .month-header{ padding: 10px; }
    }

</style>

<div id="loader"><div class="spin"></div></div>

<div id="appCapsule">
    <div class="desktop-layout">

        {{-- ── LEFT COLUMN (hero + kamera) ── --}}
        <div class="desktop-left">

            <div id="user-section">
                <a href="javascript:;" class="back-btn goBack" title="Kembali">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>

                <p class="user-greeting">Absensi Kehadiran</p>
                <h2 class="jam-besar" id="jam">00:00:00</h2>

                @if ($cek > 0)
                <span class="status-badge pulang">
                    <ion-icon name="log-out-outline"></ion-icon> Absen Pulang
                </span>
                @else
                <span class="status-badge masuk">
                    <ion-icon name="log-in-outline"></ion-icon> Absen Masuk
                </span>
                @endif
            </div>

            <div class="presence-float">
                <div class="webcam-card">
                    <div class="webcam-card-header">
                        <ion-icon name="camera-outline"></ion-icon>
                        <span>Ambil Foto</span>
                    </div>
                    <div class="webcam-frame">
                        <div class="webcam-capture"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ── END LEFT COLUMN ── --}}

        {{-- ── RIGHT COLUMN (form) ── --}}
        <div class="desktop-right">
            <div class="main-content">

                <input type="hidden" id="lokasi">

                <div class="month-header">
                    <div>
                        <div class="month-title">Lokasi Absen</div>
                        <div class="month-sub">Aktifkan GPS untuk validasi radius</div>
                    </div>
                    <div class="month-icon"><ion-icon name="location-outline"></ion-icon></div>
                </div>

                <div class="map-card">
                    <div id="map">
                        <button id="refreshLocation" class="refresh-btn">🔄 Refresh Lokasi</button>
                    </div>
                </div>

                <div class="keterangan-card">
                    @if ($cek > 0)
                    <label class="keterangan-label">Keterangan</label>
                    <textarea class="absen-textarea" id="keterangan_masuk" rows="3" placeholder="Isi keterangan kenapa kamu pulang duluan/absen diluar radius"></textarea>
                    @else
                    <label class="keterangan-label">Keterangan</label>
                    <textarea class="absen-textarea" id="keterangan_pulang" rows="3" placeholder="Isi keterangan kenapa kamu terlambat/absen diluar radius"></textarea>
                    @endif
                </div>

                @if ($cek > 0)
                <button id="takeabsen" class="btn-absen pulang">
                    <i class="fa-solid fa-camera"></i> Absen Pulang
                </button>
                @else
                <button id="takeabsen" class="btn-absen masuk">
                    <i class="fa-solid fa-camera"></i> Absen Masuk
                </button>
                @endif

            </div>
        </div>
        {{-- ── END RIGHT COLUMN ── --}}

    </div>{{-- .desktop-layout --}}
</div>

@endsection

@push('script')
    <script>

        var image = null;
        var map = null;
        var marker = null;
        var circle = null;

        Webcam.set({
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            flip_horiz: false
        });
        Webcam.attach('.webcam-capture');

        window.addEventListener('beforeunload', function () {
            try { Webcam.reset(); } catch (e) { console.log(e); }
        });

        var lokasi = document.getElementById('lokasi');

        function initMapIfNeeded(lat, lng){
            if (map) return;
            map = L.map('map').setView([lat, lng], 18);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            marker = L.marker([lat, lng]).addTo(map);
            circle = L.circle([-6.919080053798793, 107.7153742206726], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: 20
            }).addTo(map);
        }

        function updateMapPosition(lat, lng){
            lokasi.value = lat + "," + lng;
            initMapIfNeeded(lat, lng);

            if (marker) marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 18, { animate: true });
        }

        function detectLocation(){
            if(!navigator.geolocation){
                Swal.fire({
                    title:'Error!',
                    text:'Browser tidak mendukung GPS (geolocation).',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $("#refreshLocation").prop("disabled", true).text("Memperbarui...");

            navigator.geolocation.getCurrentPosition(function(position){
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                updateMapPosition(lat, lng);
            }, function(err){
                var msg = 'Gagal mendapatkan lokasi. Pastikan izin lokasi aktif.';
                if (err && err.code === 1) msg = 'Izin lokasi ditolak. Silakan aktifkan izin lokasi.';
                if (err && err.code === 2) msg = 'Lokasi tidak tersedia. Coba lagi di tempat terbuka.';
                if (err && err.code === 3) msg = 'Timeout mendapatkan lokasi. Coba lagi.';
                Swal.fire({
                    title:'Error!',
                    text: msg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });

            setTimeout(function(){
                $("#refreshLocation").prop("disabled", false).text("🔄 Refresh Lokasi");
            }, 1200);
        }

        // initial detect
        detectLocation();

        // manual refresh (button inside map)
        $("#refreshLocation").on("click", function(e){
            e.preventDefault();
            detectLocation();
        });

        $("#takeabsen").click(function(e) {
            Webcam.snap(function(uri) {
                image = uri;
            });
            var lokasi = $("#lokasi").val();
            var keterangan_masuk = $("#keterangan_masuk").val();
            var keterangan_pulang = $("#keterangan_pulang").val();
            $.ajax({
                type: 'POST',
                url: 'absen/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi,
                    keterangan_masuk:  keterangan_masuk,
                    keterangan_pulang: keterangan_pulang
                },
                cache: false,
                success: function(respond) {
                    var status = respond.split("|");
                    if(status[0] == "success"){
                      Swal.fire({
                      title:'Berhasil!',
                      text:  status[1],
                      icon: 'success',
                    })
                    setTimeout("location.href='/dashboard'", 300);
                    }else{
                      var pesanError = status[1] ? status[1] : 'Absen gagal diproses';
                      Swal.fire({
                        title:'Error!',
                        text: pesanError,
                        icon: 'error',
                        confirmButtonText: 'OK'
                      })
                    }

                }

            })

        });

        function updateJam() {
            const sekarang = new Date();
            const jam   = String(sekarang.getHours()).padStart(2, '0');
            const menit = String(sekarang.getMinutes()).padStart(2, '0');
            const detik = String(sekarang.getSeconds()).padStart(2, '0');
            document.getElementById("jam").innerHTML = jam + ":" + menit + ":" + detik;
        }
        setInterval(updateJam, 1000);
        updateJam();

        // loader (sama seperti dashboard)
        function hideLoader() {
            var l = document.getElementById('loader');
            if (!l) return;
            l.classList.add('hidden');
            setTimeout(function() { if (l.parentNode) l.parentNode.removeChild(l); }, 350);
        }
        document.addEventListener('DOMContentLoaded', function() { setTimeout(hideLoader, 300); });
        window.addEventListener('load', function() { hideLoader(); });
        setTimeout(hideLoader, 3000);

    </script>
@endpush
