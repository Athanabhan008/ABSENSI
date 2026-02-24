@extends('layouts.template_absen')

@section('content')

<style>

    :root{
        --absen-radius: 16px;
    }

    /* spacing wrapper (avoid inline margin-top) */
    .absen-page{
        margin-top: 78px;
        padding-bottom: 18px;
    }

    .absen-card{
        border-radius: var(--absen-radius);
        border: 1px solid rgba(0,0,0,.06);
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(0,0,0,.06);
        background: #fff;
    }

    .absen-card-header{
        padding: 12px 14px;
        border-bottom: 1px solid rgba(0,0,0,.06);
        background: linear-gradient(135deg, rgba(13,110,253,.10), rgba(13,110,253,.04));
    }

    .webcam-capture,
    .webcam-capture video{
        display: block;
        width: 100% !important;
        height: auto !important;
        border-radius: 0;
    }
    /* Preview seperti kamera selfie (mirror) */
    .webcam-capture video{
        transform: scaleX(-1) !important;
    }

    /* make webcam area look like a frame */
    .webcam-frame{
        background: #0b1220;
        aspect-ratio: 4 / 3;
        width: 100%;
    }
    .webcam-frame .webcam-capture{
        width: 100%;
        height: 100%;
    }

    #map{
        width: 100%;
        height: 220px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,.06);
    }

    .absen-textarea{
        /* resize: none; */
        min-height: 20px;
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,.12);
        padding: 12px 12px;
        line-height: 1.35;
    }
    .absen-textarea:focus{
        border-color: rgba(13,110,253,.55);
        box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
    }

    @media (max-width: 575.98px){
        .absen-page{ margin-top: 70px; }
        #map{ height: 190px; }
        .absen-textarea{ min-height: 20px; }
    }

</style>


 <!-- App Header -->
 <div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Absensi</div>
    <div class="right"></div>
</div>
 <!-- App Header -->
<div class="absen-page">
    <div class="container px-3">
        <input type="hidden" id="lokasi">

        <div class="absen-card mb-3">
            <div class="absen-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary" id="jam"></span>
                </div>
            </div>
            <div class="webcam-frame">
                <div class="webcam-capture"></div>
            </div>
        </div>

        <div class="mb-3">
            @if ($cek > 0)
            <button id="takeabsen" class="btn btn-danger btn-lg w-100">
                <i class="fa-solid fa-camera me-1"></i> Absen Pulang
            </button>
            @else
            <button id="takeabsen" class="btn btn-primary btn-lg w-100">
                <i class="fa-solid fa-camera me-1 mr-1"></i> Absen Masuk
            </button>
            @endif
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="absen-card p-2">
                    <div class="px-2 pt-2 pb-1">
                        <small class="text-muted">Aktifkan GPS untuk validasi radius</small>
                    </div>
                    <div id="map"></div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="absen-card p-3">
                    <textarea class="form-control absen-textarea" id="keterangan" rows="3" style="border: 1px solid #0b1220" placeholder="Keterangan"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        var image = null;

        Webcam.set({
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            flip_horiz: false
        });
        Webcam.attach('.webcam-capture');

        window.addEventListener('beforeunload', function () {
        try {
            Webcam.reset();
        } catch (e) {
            console.log(e);
        }
    });

        var lokasi = document.getElementById('lokasi');
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position){
            lokasi.value = position.coords.latitude + "," + position.coords.longitude;
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
        var circle = L.circle([-6.919080053798793, 107.7153742206726], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 10
        }).addTo(map);

        }

        function errorCallback(){

        }

        $("#takeabsen").click(function(e) {
            Webcam.snap(function(uri) {
                image = uri;
            });
            var lokasi = $("#lokasi").val();
            var keterangan = $("#keterangan").val();
            $.ajax({
                type: 'POST',
                url: 'absen/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi,
                    keterangan: keterangan
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

    </script>
@endpush
