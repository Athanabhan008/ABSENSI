@extends('layouts.template_absen')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

    /* ── WRAPPER ── */
    #appCapsule {
        max-width: 640px;
        margin: 0 auto;
    }
    @media (min-width: 900px) {
        #appCapsule { max-width: 700px; }
    }

    /* ── ALERT ── */
    .alert-banner {
        margin: 16px clamp(12px, 4vw, 20px) 0;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-banner.success { background: #D1FAE5; color: #065F46; }
    .alert-banner.error   { background: #FFE4E6; color: #9F1239; }
    .alert-banner ion-icon { font-size: 18px; flex-shrink: 0; }

    /* ── FORM CARD ── */
    .form-card {
        background: #fff;
        border-radius: 20px;
        margin: 16px clamp(12px, 4vw, 20px);
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

    .form-section-title {
        font-size: 0.7rem;
        font-weight: 700;
        color: #9CA3AF;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    /* ── FIELD ── */
    .field-group {
        margin-bottom: 14px;
    }
    .field-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: #6B7280;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .field-label ion-icon { font-size: 14px; color: #9CA3AF; }
    .field-input {
        width: 100%;
        border: 1.5px solid #E5E7EB;
        border-radius: 12px;
        padding: 11px 14px;
        font-size: 0.9rem;
        font-family: 'Inter', sans-serif;
        color: #1A1F36;
        background: #F9FAFB;
        outline: none;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    .field-input:focus {
        border-color: #4361EE;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(67,97,238,0.1);
    }
    .field-input::placeholder { color: #9CA3AF; }

    /* password hint */
    .field-hint {
        font-size: 0.68rem;
        color: #9CA3AF;
        margin-top: 5px;
    }

    /* divider */
    .form-divider {
        border: none;
        border-top: 1px solid #F3F4F6;
        margin: 18px 0;
    }

    /* ── FOTO UPLOAD ── */
    .upload-area {
        border: 2px dashed #D1D5DB;
        border-radius: 14px;
        padding: 24px 16px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
        background: #F9FAFB;
    }
    .upload-area:hover { border-color: #4361EE; background: #EEF2FF; }
    .upload-area input[type="file"] {
        position: absolute; inset: 0;
        opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .upload-icon {
        width: 48px; height: 48px;
        background: #EEF2FF;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        color: #4361EE;
        font-size: 24px;
        margin: 0 auto 10px;
    }
    .upload-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1A1F36;
        margin-bottom: 4px;
    }
    .upload-sub {
        font-size: 0.72rem;
        color: #9CA3AF;
    }
    #upload-preview {
        display: none;
        margin-top: 12px;
    }
    #upload-preview img {
        width: 80px; height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #E5E7EB;
    }
    #upload-preview span {
        display: block;
        font-size: 0.72rem;
        color: #6B7280;
        margin-top: 6px;
    }

    /* ── SUBMIT BTN ── */
    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, #4361EE, #3A56D4);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 14px;
        font-size: 0.95rem;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        box-shadow: 0 4px 14px rgba(67,97,238,0.35);
        transition: transform 0.15s, box-shadow 0.15s;
        margin-top: 4px;
    }
    .btn-submit:active { transform: scale(0.98); box-shadow: 0 2px 8px rgba(67,97,238,0.25); }
    .btn-submit ion-icon { font-size: 18px; }
</style>

<body>
<div id="appCapsule">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <a href="javascript:history.back()" class="back-btn">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
        <h1 class="page-title">Edit Profil</h1>
        <div class="page-header-icon">
            <ion-icon name="person-outline"></ion-icon>
        </div>
    </div>

    {{-- ── ALERTS ── --}}
    @php
        $messagesuccess = Session::get('success');
        $messageerrors  = Session::get('errors');
    @endphp
    @if($messagesuccess)
    <div class="alert-banner success">
        <ion-icon name="checkmark-circle-outline"></ion-icon>
        {{ $messagesuccess }}
    </div>
    @endif
    @if($messageerrors)
    <div class="alert-banner error">
        <ion-icon name="alert-circle-outline"></ion-icon>
        {{ $messageerrors }}
    </div>
    @endif

    {{-- ── FORM ── --}}
    <form action="/profile/updateprofile/{{ $user->id }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-card">
            <p class="form-section-title">Informasi Akun</p>

            <div class="field-group">
                <label class="field-label">
                    <ion-icon name="person-outline"></ion-icon> Nama Lengkap
                </label>
                <input type="text" class="field-input" name="name"
                    value="{{ $user->name }}" placeholder="Nama Lengkap" autocomplete="off">
            </div>

            <div class="field-group">
                <label class="field-label">
                    <ion-icon name="call-outline"></ion-icon> No. HP
                </label>
                <input type="text" class="field-input" name="no_hp"
                    value="{{ $user->no_hp }}" placeholder="No. HP" autocomplete="off">
            </div>

            <div class="field-group">
                <label class="field-label">
                    <ion-icon name="mail-outline"></ion-icon> Email
                </label>
                <input type="email" class="field-input" name="email"
                    value="{{ $user->email }}" placeholder="Email" autocomplete="off">
            </div>

            <hr class="form-divider">
            <p class="form-section-title">Keamanan</p>

            <div class="field-group">
                <label class="field-label">
                    <ion-icon name="lock-closed-outline"></ion-icon> Password Baru
                </label>
                <input type="password" class="field-input" name="password"
                    placeholder="Kosongkan jika tidak diubah" autocomplete="new-password">
                <p class="field-hint">Biarkan kosong jika tidak ingin mengubah password.</p>
            </div>

            <hr class="form-divider">
            <p class="form-section-title">Foto Profil</p>

            <div class="upload-area" id="uploadArea">
                <input type="file" name="foto" id="fileuploadInput" accept=".png,.jpg,.jpeg"
                    onchange="previewPhoto(this)">
                <div class="upload-icon"><ion-icon name="cloud-upload-outline"></ion-icon></div>
                <div class="upload-title">Upload Foto</div>
                <div class="upload-sub">PNG, JPG, JPEG · Maks 2MB</div>
                <div id="upload-preview">
                    <img id="preview-img" src="#" alt="Preview">
                    <span id="preview-name"></span>
                </div>
            </div>

            <br>
            <button type="submit" class="btn-submit">
                <ion-icon name="refresh-outline"></ion-icon>
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>

<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-name').textContent = input.files[0].name;
                document.getElementById('upload-preview').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
