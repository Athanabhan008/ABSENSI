@extends('layouts.template_admin')
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
</style>

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
            <div class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease">
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="w-full px-6 py-6 mx-auto">
      <div class="flex flex-wrap -mx-3">
        <div class="flex-none w-full max-w-full px-3">
          <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border" style="filter: drop-shadow(0 0 0.75rem rgb(0, 0, 0)); margin-top: 20px;">
            <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
              <h6 class="dark:text-white">Tabel Cuti</h6>
            </div>
            <div class="flex-auto px-0 pt-0 pb-2">
              <div class="p-0 overflow-x-auto">
                <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                  <thead class="align-bottom">
                    <tr>
                      <th class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70" style="text-align: center;">Nama</th>
                      <th class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70" style="text-align: center;">Jatah Cuti</th>
                      <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70" style="text-align: center;">Status & Tgl Pengajuan</th>
                      <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70" style="text-align: center;">Sisa cuti</th>
                      <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70" style="text-align: center;">Keterangan</th>
                      <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70" style="text-align: center;">Status Approve</th>
                      <th class="px-6 py-3 font-semibold capitalize align-middle bg-transparent border-b border-collapse border-solid shadow-none dark:border-white/40 dark:text-white tracking-none whitespace-nowrap text-slate-400 opacity-70" style="text-align: center;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($cuti as $item)

                    <tr>
                      <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent" style="text-align: center;">

                          <div class="flex flex-col justify-center">
                            <h6 class="mb-0 text-sm leading-normal dark:text-white">{{ $item->name }}</h6>
                          </div>

                      </td>
                      <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent" style="text-align: center;">

                          <div class="flex flex-col justify-center">
                            <h6 class="mb-0 text-sm leading-normal dark:text-white">{{ $item->jatah_cuti }}</h6>
                          </div>

                      </td>
                      <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                        <p class="mb-0 text-xs leading-tight dark:text-white dark:opacity-80 text-slate-400" style="text-align: center;">{{ $item->tgl_pengajuan }} s/d {{ $item->tgl_pengajuan_akhir }}</p>
                        <p class="mb-0 text-xs font-semibold leading-tight dark:text-white dark:opacity-80" style="text-align: center;">Total cuti {{ $item->total_hari }} hari</p>
                      </td>

                      <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent" style="text-align: center;">
                        <p class="mb-0 text-xs font-semibold leading-tight dark:text-white dark:opacity-80"></p>
                      </td>

                      <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent" style="text-align: center;">
                        <p class="mb-0 text-xs font-semibold leading-tight dark:text-white dark:opacity-80">{{ $item->keterangan }}</p>
                      </td>
                      <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent" style="text-align: center;">
                        @if($item->status_approve == 1)
                        <span class="mb-0 text-xs font-semibold leading-tight dark:text-white dark:opacity-80 badge bg-success">Approve</span>
                        @elseif($item->status_approve == 2)
                        <p class="mb-0 text-xs font-semibold leading-tight dark:text-white dark:opacity-80 badge bg-danger">Rejected</p>
                        @else
                        <p class="mb-0 text-xs font-semibold leading-tight dark:text-white dark:opacity-80 badge bg-warning">Pending</p>
                        @endif
                      </td>

                      <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent" style="text-align: center;">
                        @if ($item->status_approve == 0)
                        <button type="button" class="btn btn-primary btn-approve" data-bs-toggle="modal" data-bs-target="#modal-cuti" data-id="{{ $item->id }}">
                            <i class="fa-solid fa-arrow-up-right-from-square mr-4"></i>Approval
                        </button>
                        @else
                        <button type="button" class="btn btn-danger btn-approve" data-bs-toggle="modal" data-bs-target="#modal-cuti" data-id="{{ $item->id }}">
                            <i class="fa-solid fa-cancel mr-4"></i>Batalkan
                        </button>
                        @endif
                      </td>

                    </tr>
                   @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal -->
<div class="modal fade" id="modal-cuti" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Approval Cuti</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="form-approve-cuti" action="/izin_sakit/approve" method="POST">
            @csrf
            <input type="hidden" name="id" id="approve_cuti_id" value="">
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

<!-- Preview Modal -->
<div id="previewModal" class="preview-modal" onclick="closePreview(event)">
    <span class="preview-close" onclick="closePreview(event)">&times;</span>
    <img class="preview-content" id="previewImage" alt="Surat Sakit">
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    // Set id saat modal dibuka dari tombol Approval
    document.getElementById('modal-cuti').addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        if (btn && btn.classList.contains('btn-approve') && btn.dataset.id) {
            var id = btn.dataset.id;
            document.getElementById('approve_cuti_id').value = id;
            document.getElementById('form-approve-cuti').action = '/cuti/approve/' + id;
        }
    });

    // Submit form via AJAX dan tutup modal saat berhasil
    document.getElementById('form-approve-cuti').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        var url = form.action;
        var modalEl = document.getElementById('modal-cuti');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function (res) {
            if (res.ok || res.redirected) {
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Status izin/sakit berhasil diubah.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(function () {
                    if (res.redirected) {
                        window.location.href = res.url;
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                window.location.reload();
            }
        })
        .catch(function () {
            window.location.reload();
        });
    });
</script>

@endsection
