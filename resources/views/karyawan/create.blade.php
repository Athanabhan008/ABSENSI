@extends('layouts.template_admin')
@section('content')

<main class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl">
    <!-- Navbar -->
    <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all ease-in shadow-none duration-250 rounded-2xl lg:flex-nowrap lg:justify-start" navbar-main navbar-scroll="false">
      <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
        <nav>
          <!-- breadcrumb -->
          <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
            <li class="text-sm leading-normal">
              <a class="text-white opacity-50" href="javascript:;">Pages</a>
            </li>
            <li class="text-sm pl-2 capitalize leading-normal text-white before:float-left before:pr-2 before:text-white before:content-['/']" aria-current="page">Tables</li>
          </ol>
          <h6 class="mb-0 font-bold text-white capitalize">Tables</h6>
        </nav>

        <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
          <div class="flex items-center md:ml-auto md:pr-4">
          </div>
        </div>
      </div>
    </nav>

    <div class="w-full p-6 mx-auto">
        <div class="flex flex-wrap -mx-3">
          <div class="w-full max-w-full px-3 shrink-0 md:w-12/12 md:flex-0">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
              <div class="border-black/12.5 rounded-t-2xl border-b-0 border-solid p-6 pb-0">
                <div class="flex items-center">
                  <p class="mb-0 dark:text-white/80">Form Karyawan</p>
                </div>
              </div>
              <form action="/karyawan/store" method="POST">
                @csrf
              <div class="flex-auto p-6">
                <div class="flex flex-wrap -mx-3">
                  <div class="w-full max-w-full px-3 shrink-0 md:w-12/12 md:flex-0">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Role</span>
                        <select class="form-control" name="role" aria-label="Username" aria-describedby="basic-addon1">
                            <option value="#">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                      </div>
                  </div>

                  <div class="w-full max-w-full px-3 shrink-0 md:w-12/12 md:flex-0">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Tanggal Masuk</span>
                        <input type="date" class="form-control" aria-label="Username" name="tgl_masuk" aria-describedby="basic-addon1">
                      </div>
                  </div>

                  <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Nama</span>
                        <input type="text" class="form-control" aria-label="Username" name="name" aria-describedby="basic-addon1">
                      </div>
                  </div>
                  <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Email</span>
                        <input type="email" class="form-control" name="email" aria-label="email" aria-describedby="basic-addon1">
                      </div>
                  </div>
                  <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0 mt-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">No. Handphone</span>
                        <input type="text" class="form-control" name="no.hp" aria-label="Username" aria-describedby="basic-addon1">
                      </div>
                  </div>
                  <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0 mt-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Password</span>
                        <input type="password" class="form-control" name="password" aria-describedby="basic-addon1">
                      </div>
                  </div>

                  <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0 mt-4">
                   <button id="btn_submit" type="submit" class="btn btn-success">Simpan</button>
                  </div>
                </div>
              </div>
            </form>
            </div>
          </div>
        </div>
      </div>
  </main>

@endsection
