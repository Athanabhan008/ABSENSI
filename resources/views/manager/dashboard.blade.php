@extends('layouts.template_absen')

@section('content')

<style>
    .logout{
        position: absolute;
        color: white;
        font-size: 30px;
        text-decoration: none;
        right: 30px;
        margin-top: 40px;
}
</style>

<body style="background-color:#e9ecef;">

    <!-- loader -->
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <!-- * loader -->



    <!-- App Capsule -->
    <div id="appCapsule">
        <div class="section" id="user-section">
            <a href="/logout" class="logout">
                <ion-icon name="log-out-outline"></ion-icon>
            </a>
            <div id="user-detail">
                <div class="avatar">
                    <img src="{{ asset('absensi/assets/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="imaged w64 rounded">
                </div>
                <div id="user-info">
                    <h2 id="user-name">{{ auth()->user()->name }}</h2>
                    <span id="user-role">Head of IT</span>
                </div>
            </div>
        </div>

        <div class="section" id="menu-section">
            <div class="card">
                <div class="card-body text-center">
                    <div class="list-menu">
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="" class="green" style="font-size: 40px;">
                                    <ion-icon name="person-sharp"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Profil</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 35px;">{{ $sisa_cuti }}</p>
                                <small class="text-muted" style="font-size: 0.75rem;">sisa dari {{ $jatah_cuti }}</small>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Cuti</span>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">terpakai {{ $jmlcuti }}</small>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="" class="warning" style="font-size: 40px;">
                                    <ion-icon name="document-text"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Histori</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="" class="orange" style="font-size: 40px;">
                                    <ion-icon name="location"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                Lokasi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section mt-2" id="presence-section">
            <div class="todaypresence">
                <div class="row">
                    <div class="col-6">
                        <div class="card gradasigreen">
                            <div class="card-body">
                                <div class="presencecontent">
                                    <div class="iconpresence">
                                        @if ($absensihariini != null)
                                        @php
                                            $path = Storage::url('uploads/absensi/' . $absensihariini->foto_masuk);
                                        @endphp
                                            <img src="{{ url($path) }}" width="75px" alt="">
                                        @else
                                        <ion-icon name="camera"></ion-icon>
                                        @endif
                                    </div>
                                    <div class="presencedetail">
                                        <h4 class="presencetitle">Masuk</h4>
                                        <span>{{ $absensihariini != null ? $absensihariini->jam_masuk : 'Belum Absen' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card gradasired">
                            <div class="card-body">
                                <div class="presencecontent">
                                    <div class="iconpresence">
                                        @if ($absensihariini != null && $absensihariini->foto_keluar != null)
                                        @php
                                            $path = Storage::url('uploads/absensi/' . $absensihariini->foto_keluar);
                                        @endphp
                                            <img src="{{ url($path) }}" width="75px" alt="">
                                        @else
                                        <ion-icon name="camera"></ion-icon>
                                        @endif
                                    </div>
                                    <div class="presencedetail">
                                        <h4 class="presencetitle">Pulang</h4>
                                        <span>{{ $absensihariini != null && $absensihariini->jam_keluar != null ? $absensihariini->jam_keluar : 'Belum Absen' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="rekapabsensi">
                <h3 class="mt-2">Rekap Absensi Bulan {{ $bulan_nama_carbon }} Tahun {{ $tahunSekarang }}</h3>
                <div class="row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem;">
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 10px; font-size:0.6rem; z-index: 999;">{{ $rekappresensi->jmlhadir }}</span>
                                <ion-icon name="accessibility-outline" style="font-size: 30px;" class="text-primary mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem; font-weight: 500;">HADIR</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem;">
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 10px; font-size:0.6rem; z-index: 999;">{{ $jmlcuti ?? 0 }}</span>
                                <ion-icon name="newspaper-outline" style="font-size: 30px;" class="text-success mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem; font-weight: 500;">IZIN</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem;">
                                @foreach ($rekapsakit as $data)
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 10px; font-size:0.6rem; z-index: 999;">{{ $data->jmlsakit }}</span>
                                @endforeach
                                <ion-icon name="medkit-outline" style="font-size: 30px;" class="text-warning mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem; font-weight: 500;">SAKIT</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem;">
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 10px; font-size:0.6rem; z-index: 999;">{{ $rekappresensi->jmlterlambat }}</span>
                                <ion-icon name="alarm-outline" style="font-size: 30px;" class="text-danger mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem; font-weight: 500;">TELAT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="presencetab mt-2">
                <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                    <ul class="nav nav-tabs style1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                                Bulan Ini
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                                Leaderboard
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content mt-2" style="margin-bottom:100px;">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <ul class="listview image-listview">
                            @foreach ($historibulanini as $item)
                            <li>
                                <div class="item">
                                    <div class="icon-box bg-primary">
                                        <ion-icon name="finger-print-outline"></ion-icon>
                                    </div>
                                    <div class="in">
                                        <div>{{ date("d-m-Y", strtotime($item->tgl_absen)) }}</div>
                                        <span class="badge badge-success">{{ $item->jam_masuk }}</span>
                                        <span class="badge badge-danger">{{ $absensihariini != null && $item->jam_keluar != null ? $item->jam_keluar : 'Belum Absen' }}</span>
                                    </div>
                                </div>
                            </li>

                            @endforeach

                        </ul>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel">
                        <ul class="listview image-listview">
                            @foreach ($leaderboard as $lb)

                            <li>
                                <div class="item">
                                    <img src="{{ asset('absensi/assets/img/sample/avatar/avatar1.jpg') }}" alt="image" class="image">
                                    <div class="in">
                                        <div>{{ $lb->name }}</div>
                                        <span class="badge {{ $lb->jam_masuk < "08:15" ? "bg-success" : "bg-danger" }}">{{ $lb->jam_masuk }}</span>
                                    </div>
                                </div>
                            </li>

                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- * App Capsule -->




@endsection
