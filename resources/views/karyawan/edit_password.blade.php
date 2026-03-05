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
                  <h6>Ubah Password Karyawan</h6>
            </div>
            <div class="card-body px-4 pt-3 pb-3">
              <div class="table-responsive p-0">

                <form action="/karyawan/update-password/{{ $user->id }}" method="POST">
                    @csrf
                
                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1" style="background-color: rgb(209, 209, 209)">Password</span>
                            <input type="password" class="form-control"  name="password" aria-label="Username" aria-describedby="basic-addon1">
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
