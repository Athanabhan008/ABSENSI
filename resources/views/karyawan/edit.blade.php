@extends('layouts.template_admin')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.bootstrap5.css">

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
              <div class="card-header pb-0">
                  <h6>Ubah Data Karyawan</h6>
            </div>
            <div class="card-body px-4 pt-3 pb-3">
              <div class="table-responsive p-0">

                <form action="/karyawan/update/{{ $user->id }}" method="POST">
                    @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="background-color: rgb(209, 209, 209)">Role</span>
                            <select class="form-control" name="role" aria-label="Username" aria-describedby="basic-addon1">
                                <option value="{{ $user->role }}">{{ $user->role }}</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="background-color: rgb(209, 209, 209)">Tanggal Masuk</span>
                            <input type="date" class="form-control" aria-label="Username" name="tgl_masuk" aria-describedby="basic-addon1" value="{{ $user->tgl_masuk }}">
                          </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="background-color: rgb(209, 209, 209)">Nama Lengkap</span>
                            <input type="text" class="form-control" placeholder="Username" name="name" aria-label="Username" aria-describedby="basic-addon1" value="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="background-color: rgb(209, 209, 209)">Email</span>
                            <input type="text" class="form-control" placeholder="Email" name="email" aria-label="Username" aria-describedby="basic-addon1" value="{{ $user->email }}">
                          </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="background-color: rgb(209, 209, 209)">No.Handphone</span>
                            <input type="number" class="form-control" placeholder="No.handphone" name="no_hp" aria-label="Username" aria-describedby="basic-addon1" value="{{ $user->no_hp }}">
                        </div>
                    </div>
                </div>
                <a class="btn btn-secondary" href="{{ url()->previous() }}">Kembali</a>
                <button class="btn btn-success" type="submit">Simpan</button>

                </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


    </div>
  </main>




@endsection
