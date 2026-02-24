@extends('layouts.template_absen')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">

<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Histori Absen</div>
    <div class="right"></div>
</div>
 <!-- App Header -->

 <div class="row" style="margin-top: 60px">
    <div class="col">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <form id="form_filter" method="GET" action="{{ route('absen.histori') }}">
                        <div class="modal-body">
                            <div class="input-group mb-3">
                                <input type="text" name="periode_start" id="periode_start" class="form-control form-control-lg pl-3 yearmonthpicker" placeholder="Pilih Bulan (YYYY-MM)" autocomplete="off" value="{{ $periode_start ?? '' }}">
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success submit-filter btn-block">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
 </div>

 <div class="section mt-2 mb-2">
    <div class="presencetab">
        <ul class="listview image-listview">
            @if(isset($absensi) && $absensi->count() > 0)
                @foreach($absensi as $item)
                <li>
                    <div class="item">
                        <div class="in">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <strong>{{ date("d-m-Y", strtotime($item->tgl_absen)) }}</strong>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">
                                        <small class="text-muted">Jam Masuk</small>
                                        <div>
                                            @if($item->jam_masuk)
                                                <span class="badge badge-success">{{ $item->jam_masuk }}</span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <small class="text-muted">Foto Masuk</small>
                                        <div class="mt-1">
                                            @if($item->foto_masuk)
                                                @php
                                                    $pathMasuk = Storage::url('uploads/absensi/' . $item->foto_masuk);
                                                @endphp
                                                <img src="{{ url($pathMasuk) }}" alt="Foto Masuk" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px; margin-bottom: 45px;">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">
                                        <small class="text-muted">Jam Keluar</small>
                                        <div>
                                            @if($item->jam_keluar)
                                                <span class="badge badge-danger">{{ $item->jam_keluar }}</span>
                                            @else
                                                <span class="badge badge-warning">Belum Absen</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <small class="text-muted">Foto Keluar</small>
                                        <div class="mt-1">
                                            @if($item->foto_keluar)
                                                @php
                                                    $pathKeluar = Storage::url('uploads/absensi/' . $item->foto_keluar);
                                                @endphp
                                                <img src="{{ url($pathKeluar) }}" alt="Foto Keluar" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            @else
                <li>
                    <div class="item">
                        <div class="in">
                            <div class="text-center py-3">
                                <span class="text-muted">Tidak ada data absensi</span>
                            </div>
                        </div>
                    </div>
                </li>
            @endif
        </ul>
    </div>
 </div>

 @push('scripts')

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="../../admin/assets/js/plugins/bootstrap-datepicker.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>


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
