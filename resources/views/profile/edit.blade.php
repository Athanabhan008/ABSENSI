@extends('layouts.template_absen')

@section('content')

<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Profile</div>
    <div class="right"></div>
</div>
<!-- App Header -->


<div class="section mt-2">

    @php
        $messagesuccess = Session::get('success');
        $messageerrors = Session::get('errors');
    @endphp

    @if ($messagesuccess)
        <div class="alert alert-success">
            {{ $messagesuccess }}
        </div>
    @endif

    @if ($messageerrors)
        <div class="alert alert-danger">
            {{ $messageerrors }}
        </div>
    @endif

</div>


<form action="/profile/updateprofile/{{ $user->id }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="section">

    <div class="form-group boxed">
        <div class="input-wrapper">
            <input type="text" class="form-control"
                   value="{{ $user->name }}"
                   name="name"
                   placeholder="Nama Lengkap">
        </div>
    </div>

    <div class="form-group boxed">
        <div class="input-wrapper">
            <input type="text" class="form-control"
                   value="{{ $user->no_hp }}"
                   name="no_hp"
                   placeholder="No. HP">
        </div>
    </div>

    <div class="form-group boxed">
        <div class="input-wrapper">
            <input type="email" class="form-control"
                   value="{{ $user->email }}"
                   name="email"
                   placeholder="Email">
        </div>
    </div>

    <div class="form-group boxed">
        <div class="input-wrapper">
            <input type="password" class="form-control"
                   name="password"
                   placeholder="Password">
        </div>
    </div>

    <div class="custom-file-upload" id="fileUpload1">
        <input type="file" name="foto" id="fileuploadInput" accept=".png,.jpg,.jpeg">
        <label for="fileuploadInput">
            <span>
                <strong>
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                    <i>Tap to Upload</i>
                </strong>
            </span>
        </label>
    </div>

    <div class="form-group boxed mt-2">
        <div class="input-wrapper">
            <button type="submit" class="btn btn-primary btn-block">
                <ion-icon name="refresh-outline"></ion-icon>
                Update
            </button>
        </div>
    </div>

</div>

</form>

@endsection
