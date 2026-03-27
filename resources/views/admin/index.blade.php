@extends('layouts.template_admin')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')

<style>
    /* Thumbnail */
.preview-thumb {
    width: 100px;
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

.link-menu{
    text-decoration: none;
}

.datepicker table {
    margin: 5px;
}

.datepicker table tr td,
.datepicker table tr th {
    width: 42px;
    height: 42px;
    line-height: 42px;
    border-radius: 6px;
    margin: 2px;
}
.datepicker table tr td.active {
    background: #0d6efd;
    color: white;
    border-radius: 3px;
}
</style>

<!--
=========================================================
* Soft UI Dashboard 3 - v1.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->

    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-12 col-12">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-12">
              <div class="card">
                <span class="mask bg-success opacity-10 border-radius-lg"></span>
                <div class="card-body p-3 position-relative">
                  <div class="row">
                    <div class="col-8 text-start">
                      <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                        <i class="fa-solid fa-people-group text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                      <h5 class="text-white font-weight-bolder mb-0 mt-3">
                        {{ $rekappresensi->jmlhadir }}
                      </h5>
                      <span class="text-white text-sm">Karyawan Hadir</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12 mt-4 mt-md-0">
              <div class="card">
                <span class="mask bg-danger opacity-10 border-radius-lg"></span>
                <div class="card-body p-3 position-relative">
                  <div class="row">
                    <div class="col-8 text-start">
                      <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                        <i class="fa-regular fa-clock text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                      <h5 class="text-white font-weight-bolder mb-0 mt-3">
                        {{ $rekappresensi->jmlterlambat }}
                      </h5>
                      <span class="text-white text-sm">Karyawan Terlambat</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-lg-6 col-md-6 col-12">
              <div class="card">
                <span class="mask bg-primary opacity-10 border-radius-lg"></span>
                <div class="card-body p-3 position-relative">
                  <div class="row">
                    <div class="col-8 text-start">
                      <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                        <i class="fa-regular fa-calendar-check text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                      <h5 class="text-white font-weight-bolder mb-0 mt-3">
                        @foreach ($rekapcuti as $item)
                        <h5 class="mb-2 font-bold dark:text-white">{{ $item->jmlcutitoday }}</h5>
                        @endforeach
                      </h5>
                      <span class="text-white text-sm">Karyawan Cuti</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12 mt-4 mt-md-0">
              <div class="card">
                <span class="mask bg-warning opacity-10 border-radius-lg"></span>
                <div class="card-body p-3 position-relative">
                  <div class="row">
                    <div class="col-8 text-start">
                      <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                        <i class="fa-regular fa-face-tired text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                      <h5 class="text-white font-weight-bolder mb-0 mt-3">
                        @foreach ($rekapsakit as $item)
                        <h5 class="mb-2 font-bold dark:text-white">{{ $item->jmlsakittoday }}</h5>
                        @endforeach
                      </h5>
                      <span class="text-white text-sm">Karyawan Sakit</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="row mt-4">
        <div class="col-lg-12 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-12">
                  <div class="d-flex flex-column h-100">

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-regular fa-calendar"></i></span>
                        <input type="text" name="periode_start" id="periode_start" value="{{ $periode_start ?? date('Y-m-d') }}" class="form-control form-control-lg pl-3 tanggal" placeholder="Pilih Tanggal (YYYY-MM-DD)" autocomplete="off" aria-describedby="basic-addon1">
                    </div>

                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-0 overflow-x-auto">
                          <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                            <thead class="align-bottom">
                              <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">No</th>
                                <th class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Nama</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Jam Masuk</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Foto Masuk</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Jam Pulang</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Foto Pulang</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Keterangan</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Map</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Status Approve</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 text-center">Action</th>
                              </tr>
                            </thead>
                            <tbody id="loadabsensi"></tbody>
                          </table>
                        </div>
                      </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

  <div class="modal fade" id="modal-map" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Lokasi Absensi</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="loadmap"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div id="previewModal" class="preview-modal" onclick="closePreview(event)">
    <span class="preview-close" onclick="closePreview(event)">&times;</span>
    <img class="preview-content" id="previewImage" alt="Surat Sakit">
</div>


<!-- Modal -->
<div class="modal fade" id="modal-approval" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Approval Absensi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="form-approve-absen" action="/absen/approve/" method="POST">
                @csrf
                <input type="hidden" name="id" id="approve_absen_id" value="">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <select name="status_approve" id="status_approve" class="form-select">
                                <option value="#">--- Pilih Approval ---</option>
                                <option value="1">Approve</option>
                                <option value="2">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        </div>
      </div>
    </div>
  </div>

@push('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="../../admin/assets/js/plugins/bootstrap-datepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>


<script>
  function openPreview(src) {
      const modal = document.getElementById('previewModal');
      const image = document.getElementById('previewImage');
      image.src = src;
      modal.style.display = 'block';
  }

  function closePreview(event) {
      // Prevent closing when clicking the image itself
      if (event && event.target && event.target.id === 'previewImage') return;
      document.getElementById('previewModal').style.display = 'none';
  }
</script>

<script>
$(document).ready(function () {
  $('.tanggal').datepicker({
    format: "yyyy-mm-dd",
    minViewMode: "days",
    startView: "days",
    autoclose: true,
    todayHighlight: true,
    container: "body"
  });

  window.loadabsensi = function(){
      var tanggal = $('.tanggal').val();
      $.ajax({
          type: 'POST',
          url: '/getabsensi',
          data: {
              _token: "{{ csrf_token() }}",
              tanggal : tanggal
          },
          cache:false,
          success: function(respond) {
              $("#loadabsensi").html(respond);
          }
      })
  }

  $('.tanggal').change(function(e){
      loadabsensi();
  })
  loadabsensi();

  function initDashboardAbsensiMap() {
    var el = document.getElementById('mapAbsensi');
    if (!el || typeof L === 'undefined') return;
    var lat = parseFloat(el.dataset.lat);
    var lng = parseFloat(el.dataset.lng);
    if (isNaN(lat) || isNaN(lng)) return;
    if (window._dashboardAbsensiMap) {
      try { window._dashboardAbsensiMap.remove(); } catch (e) {}
      window._dashboardAbsensiMap = null;
    }
    window._dashboardAbsensiMap = L.map(el).setView([lat, lng], 18);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(window._dashboardAbsensiMap);
    L.marker([lat, lng]).addTo(window._dashboardAbsensiMap);
    L.circle([-6.919080053798793, 107.7153742206726], {
      color: 'red',
      fillColor: '#f03',
      fillOpacity: 0.5,
      radius: 20
    }).addTo(window._dashboardAbsensiMap);
    setTimeout(function () {
      if (window._dashboardAbsensiMap) window._dashboardAbsensiMap.invalidateSize();
    }, 200);
  }

  $(document).on('click', '.showmap', function (e) {
    e.preventDefault();
    var id = $(this).attr('id');
    $.ajax({
      type: 'POST',
      url: '/showmap',
      data: {
        _token: "{{ csrf_token() }}",
        id: id
      },
      cache: false,
      success: function (respond) {
        if (window._dashboardAbsensiMap) {
          try { window._dashboardAbsensiMap.remove(); } catch (err) {}
          window._dashboardAbsensiMap = null;
        }
        $('#loadmap').html(respond);
        var modalEl = document.getElementById('modal-map');
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalEl.addEventListener('shown.bs.modal', function () {
          initDashboardAbsensiMap();
        }, { once: true });
        modal.show();
      }
    });
  });
});


  // Set id saat modal dibuka dari tombol Approval
  document.getElementById('modal-approval').addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        if (btn && btn.classList.contains('btn-approve') && btn.dataset.id) {
            var id = btn.dataset.id;
            document.getElementById('approve_absen_id').value = id;
            document.getElementById('form-approve-absen').action = '/absen/approve/' + id;
        }
    });

    document.getElementById('form-approve-absen').addEventListener('submit', function (e) {
    e.preventDefault();

    let form = this;
    let formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {


            let modalEl = document.getElementById('modal-approval');
            let modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();


            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());


            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });


            loadabsensi();

        } else {
            Swal.fire('Error', 'Gagal update', 'error');
        }

    })
    .catch(() => {
        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
    });
});

</script>
@endpush


@endsection
