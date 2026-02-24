@extends('layouts.template_absen')
@section('content')

<style>
    /* Thumbnail */
.preview-thumb {
    width: 80px;
    height: auto;
    cursor: pointer;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,.3);
    transition: transform .2s;
}

.preview-thumb:hover {
    transform: scale(1.05);
}

/* Modal background */
.preview-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.9);
}

/* Full image */
.preview-content {
    display: block;
    margin: auto;
    max-width: 90%;
    max-height: 90%;
    margin-top: 3%;
    border-radius: 6px;
    margin-top: 50px;
}

/* Close button (X) */
.preview-close {
    position: absolute;
    top: 15px;
    right: 25px;
    color: #fff;
    font-size: 35px;
    font-weight: bold;
    cursor: pointer;
    transition: .2s;
}

.preview-close:hover {
    color: #ff4d4d;
}

:root{
    --bottom-safe: calc(80px + env(safe-area-inset-bottom));
}

.list-container{
    padding-bottom: calc(var(--bottom-safe) + 100px);
}

</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">

<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Izin/sakit</div>
    <div class="right"></div>
</div>
 <!-- App Header -->

 <div class="row" style="margin-top: 70px">
    <div class="col">
        @php
        $messagesuccess = Session::get('success');
        $messageerrors = Session::get('errors');
        @endphp
        @if (Session::get('success'))
        <div class="alert alert-success">
            {{ $messagesuccess }}
        </div>
        @endif
        @if (Session::get('errors'))
            <div class="alert alert-error">
                {{ $messageerrors }}
            </div>
        @endif
    </div>
</div>

<div class="row list-container" style="margin-top: 45px">
    <div class="col">
        @foreach($dataizin as $item)
       <ul class="listview image-listview">
        <li>
            <div class="item">
                <div class="in">
                    <div>
                        <b>{{ \Carbon\Carbon::parse($item->tgl_pengajuan)->format('d-m-Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($item->tgl_pengajuan_akhir)->format('d-m-Y') }}
                             ({{ $item->status }})</b><br>
                        <small class="text-muted">{{ $item->keterangan }}</small>
                    </div>
                    <a href="javascript:void(0)" class="preview-link"
                    onclick="openPreview('{{ Storage::url('uploads/surat_sakit/'.$item->foto_surat) }}')">
                     <img src="{{ Storage::url('uploads/surat_sakit/'.$item->foto_surat) }}" class="preview-thumb">
                 </a>
                    <div id="imagePreviewModal" class="preview-modal" onclick="closePreview()">
                        <span class="preview-close" onclick="event.stopPropagation(); closePreview()">&times;</span>
                        <img class="preview-content" id="previewImage">
                    </div>

                    @if ($item->status_approve == 0)
                    <span class="badge bg-warning">Pending</span>
                    @elseif($item->status_approve == 1)
                    <span class="badge bg-success">Approve</span>
                    @elseif($item->status_approve == 2)
                    <span class="badge bg-danger">Decline</span>
                    @endif
                </div>
            </div>
        </li>
       </ul>
        @endforeach
    </div>
</div>

 <div class="fab-button bottom-right" style="margin-bottom: 70px">
    <a href="/izin_sakit/create" class="fab">
        <ion-icon name="add-outline"></ion-icon>
    </a>
 </div>

 @push('scripts')

 <script>
    function openPreview(imageUrl) {
        document.getElementById("imagePreviewModal").style.display = "block";
        document.getElementById("previewImage").src = imageUrl;
    }

    function closePreview() {
        document.getElementById("imagePreviewModal").style.display = "none";
    }
    </script>

 @endpush

 @endsection
