@extends('layouts.template_admin')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" integrity="sha512-xrbX64SIXOxo5cMQEDUQ3UyKsCreOEq1Im90z3B7KPoxLJ2ol/tCT0aBhuIzASfmBVdODioUdUPbt5EDEXmD9g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">

<style>
    table.dataTable tbody tr.selected {
        background-color: #58a2f1 !important;
        color: white;
    }

    .select2-container {
        z-index: 9999;
    }

    .select2-dropdown {
        z-index: 9999;
    }

    /* Styling untuk row total */
    .total-row {
        background-color: #f8f9fa !important;
        font-weight: bold !important;
        border-top: 2px solid #dee2e6 !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    .total-row td {
        background-color: #f8f9fa !important;
        font-weight: bold !important;
    }

    .total-row:hover {
        background-color: #e9ecef !important;
    }

    .total-row:hover td {
        background-color: #e9ecef !important;
    }

    /* Styling untuk grand total row */
    .grand-total-row {
        background-color: #e3f2fd !important;
        font-weight: bold !important;
        border-top: 3px solid #2196f3 !important;
        border-bottom: 2px solid #2196f3 !important;
    }

    .grand-total-row td {
        background-color: #e3f2fd !important;
        font-weight: bold !important;
    }

    .grand-total-row:hover {
        background-color: #bbdefb !important;
    }

    .grand-total-row:hover td {
        background-color: #bbdefb !important;
    }

    /* Mencegah row total dan grand total bisa dipilih */
    .total-row, .grand-total-row {
        cursor: default !important;
    }

    .total-row:hover, .grand-total-row:hover {
        background-color: #f8f9fa !important;
    }

    .grand-total-row:hover {
        background-color: #e3f2fd !important;
    }

    /* Styling untuk scroll pada DataTables */
    .dataTables_scrollBody {
        overflow-y: auto !important;
        overflow-x: auto !important;
    }

    .dataTables_scrollHead {
        overflow-x: auto !important;
        overflow-y: hidden !important;
    }

    .dataTables_wrapper .dataTables_scroll {
        clear: both;
    }

    /* Memastikan header tetap terlihat saat scroll */
    .dataTables_scrollHead table {
        margin-bottom: 0 !important;
        border-collapse: separate !important;
    }

    /* Memastikan scroll bar terlihat */
    .dataTables_scrollBody::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    <div class="page-content container-fluid py-4">
        <div class="card ccard">
            <div class="card-header align-middle border-t-3 brc-primary-tp3" style="border-bottom: 1px solid #e0e5e8 !important;">
                <h4 class="card-title text-dark-m3 mt-2">

                </h4>
            </div>

            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="form_filter">

                            {{-- <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light">User</span>
                                </div>

                                <select name="cmb_user" id="cmb_user" class="form-control"></select>
                            </div> --}}

                            <div class="input-group mb-2">
                                <div class="input-group-prepend" style="height: 39px;">
                                    <span class="input-group-text bg-light">
                                        Periode Absen
                                    </span>
                                </div>
                                <input type="text" name="periode_start" id="periode_start" class="form-control form-control-lg pl-3 yearmonthpicker" placeholder="Mulai (YYYYMM)" autocomplete="off">
                                <input type="text" name="periode_end" id="periode_end" class="form-control form-control-lg pl-3 yearmonthpicker" placeholder="Sampai (YYYYMM)" autocomplete="off">

                                <div class="input-group-append">
                                    <button class="btn btn-info" id="btnCekData" type="button">
                                        <i class="fa fa-arrow-right mr-1"></i>
                                        Cek Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row" id="dv_table">
                    <div class="col-12">
                        <div class="card mb-4">


                            <div class="card-body px-0 pt-0 pb-2">

                                <div class="text-right px-4">
                                    <div class="btn-group">

                                        <button onclick="cetakPDF()" id="btn_pdf" type="submit" class="btn btn-danger mr-3">Cetak PDF <i class="fa-solid fa-file-pdf"></i></button>

                                    </div>
                                </div>

                                <div class="p-0" style="overflow: visible;">
                                    <table id="datatable" class="table table-striped table-bordered basic-datatables">
                                        <thead style="background-color: #1E3135; color: white;">
                                            <tr>
                                            <th style="color: white;" class="text-uppercase text-xxs font-weight-bolder opacity-7">No</th>
                                            <th style="color: white;" class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">TGL Absen</th>
                                            <th style="color: white;" class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Nama</th>
                                            <th style="color: white;" class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">No.HP</th>
                                            <th style="color: white;" class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Jam Masuk</th>
                                            <th style="color: white;" class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Jam Keluar</th>
                                            <th style="color: white;" class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Keterangan</th>
                                            <th style="color: white;" class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <footer class="footer pt-3  ">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                    document.write(new Date().getFullYear())
                </script>,
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">MBS</a>
                for a better web.
                </div>
            </div>
            </div>
        </div>
    </footer>

</main>

<div id="modal_approval" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg radius-4">
        <!-- Modal content-->
        <div class="modal-content radius-4">
            <div class="modal-header btn-success radius-t-4">

                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body ace-scrollbar">
                <div class="card ccard">
                    <div class="card-header">

                    </div>
                </div>
            </div>
            <div class="modal-footer radius-b-4">
                <button type="button" id="btnAction" class="btn btn-success text-120 radius-2">
                    <i class="fa fa-save"></i>
                    Proses Data
                </button>
            </div>
        </div>

    </div>
</div>


  @push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="../../admin/assets/js/plugins/bootstrap-datepicker.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script>

    </script>


<script>

console.log("kipak");

window.defaultUrl = '{{ url('/approval/') }}/';

let modal = $("#formModal");
let table;

$('.yearmonthpicker').datepicker({
    format: "yyyymm",
    minViewMode: "months",
    startView: "years",
    autoclose: true
});
$(document).ready(function() {
    $('#dv_table').hide();

    $('#btnCekData').on('click', function() {
        // Validasi rentang bulan
        const ps = ($('#periode_start').val() || '').trim();
        const pe = ($('#periode_end').val() || '').trim();

        if (!ps || !pe) {
            Swal.fire('Peringatan', 'Silakan pilih bulan mulai dan bulan akhir.', 'warning');
            return;
        }
        if (ps.length !== 6 || pe.length !== 6 || !/^\d{6}$/.test(ps) || !/^\d{6}$/.test(pe)) {
            Swal.fire('Peringatan', 'Format periode harus YYYYMM.', 'warning');
            return;
        }
        if (ps > pe) {
            Swal.fire('Peringatan', 'Periode mulai tidak boleh lebih besar dari periode akhir.', 'warning');
            return;
        }

        // Pastikan area tabel terlihat walaupun hasil 0 baris.
        $('#dv_table').show();

        // Simpan referensi ke tombol
        let btnCekData = $(this);
        let originalContent = btnCekData.html();

        // Tampilkan loading pada tombol
        btnCekData.html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');
        btnCekData.prop('disabled', true);

        // Tampilkan loading SweetAlert
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengambil data, harap tunggu',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        // Panggil viewDatatable dengan callback untuk menangani selesai loading
        viewDatatable(function() {
            // Restore tombol ke kondisi semula
            btnCekData.html(originalContent);
            btnCekData.prop('disabled', false);

            // Tutup loading SweetAlert
            Swal.close();
        });
    });

    $('#btn_approve').on('click', function() {
        $("#modal_approval").modal("show");
    });





    $('.submit-filter').on('click', function() {
        viewDatatable();
    });

    $('select[name=cmb_laundry').on('select2:select', function (e) {
        var data = e.params.data;
        // alert(data)
        $('#harga').val(data.harga);
    });

    $("#btn-edit").on("click", function () {
        let selected = table.row('.selected').data();

        console.log(selected);
        if (_.isEmpty(selected) ||  selected == undefined) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Pilih Data Terlebih Dahulu',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }

        modal.find("input[name=_type]").val("update");
        modal.find("input[name=id]").val(selected.id);
        modal.find("input[name=nama_projek]").val(selected.nama_projek);
        modal.find("input[name=nama_client]").val(selected.nama_client);
        modal.find("select[name=jenis_pr]").val(selected.jenis_pr);

        // Sembunyikan field nomor_pr dan tombol generate saat mode edit
        $('.input-group:has(#nomor_pr)').hide();
        $('#btn-generate-pr').hide();

        resetErrors();
        modal.modal("show");
    });


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    modal.find("form").on("submit", function(ev) {
        ev.preventDefault();

        let submitButton = $(this).find("[type=submit]");
        let originalContent = submitButton.html();
        submitButton.html('<i class="fa fa-spin fa-spinner"></i> Menyimpan...');
        submitButton.prop("disabled", true);

        let type = $("[name=_type]").val();
        let id = $("[name=id]").val();
        let url = type == "create" ? defaultUrl + "create" : defaultUrl + "update/" + id;

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                // Reset form terlebih dahulu
                $('#form_sound')[0].reset();

                // Reload table
                table.ajax.reload();

                // Tutup modal menggunakan helper function
                closeModal();

                // Tampilkan SweetAlert
                Swal.fire({
                    title: 'Sukses',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        table.ajax.reload();
                    }
                });
            },
            error: function(jqXHR) {
                if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                    let errors = jqXHR.responseJSON.errors;
                    for (let field in errors) {
                        let el = $([name="${field}"]);
                        el.addClass("is-invalid");
                        el.next('.invalid-feedback').text(errors[field]);
                    }
                }
                alert('Terjadi kesalahan saat menyimpan data');
            },
            complete: function() {
                submitButton.html(originalContent);
                submitButton.prop("disabled", false);
            }
        });
    });

    // Tambahkan event handler untuk tombol delete
    $("#btn-delete").on("click", function () {
        let selected = table.row('.selected').data();

        if (_.isEmpty(selected) ||  selected == undefined) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Pilih Data Terlebih Dahulu',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Konfirmasi delete dengan SweetAlert
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request delete
                $.ajax({
                    url: defaultUrl + "delete/" + selected.id,
                    type: 'POST',
                    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                    success: function(response) {
                        if(typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Sukses!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    table.ajax.reload();
                                }
                            });
                        } else {
                            alert(response.message);
                            table.ajax.reload();
                        }
                        $('#form_sound')[0].reset();
                    },
                    error: function(jqXHR) {
                        let message = 'Terjadi kesalahan saat menghapus data';
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            message = jqXHR.responseJSON.message;
                        }
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    });


    window.cetakPDF = function() {
    const ps = ($('#periode_start').val() || '').trim();
    const pe = ($('#periode_end').val() || '').trim();

    // Validasi sama seperti tombol Cek Data
    if (!ps || !pe) {
        Swal.fire('Peringatan', 'Silakan pilih bulan mulai dan bulan akhir.', 'warning');
        return;
    }
    if (ps.length !== 6 || pe.length !== 6 || !/^\d{6}$/.test(ps) || !/^\d{6}$/.test(pe)) {
        Swal.fire('Peringatan', 'Format periode harus YYYYMM.', 'warning');
        return;
    }
    if (ps > pe) {
        Swal.fire('Peringatan', 'Periode mulai tidak boleh lebih besar dari periode akhir.', 'warning');
        return;
    }

    const url = "{{ route('report_absen/cetakpdf') }}" +
        "?periode_start=" + encodeURIComponent(ps) +
        "&periode_end=" + encodeURIComponent(pe);

    window.open(url, '_blank');
}
    // Tambahkan event handler untuk tombol close
    $('.close, .btn-secondary').click(function() {
        closeModal();
    });

    // Event handler ketika modal akan ditutup
    $('#formModal').on('hide.bs.modal', function () {
        closeModal();
    });

    collectionS2Search();

});

function viewDatatable(callback) {
    table = $('.basic-datatables').DataTable({
        ajax: {
            url: "{{ route('report_absen/datatable') }}",
            "type": "post",
            "data": function (d) {
                var formData = $("#form_filter").serializeArray();
                $.each(formData, function (key, val) {
                    d[val.name] = val.value;
                });
                d['_token'] = '{{ csrf_token() }}';
            }
        },
        dom: 't', // Hapus paging dari dom
        paging: false, // Nonaktifkan paging
        "bInfo" : false,
        destroy: true,
        serverSide: true,
        processing: true,
        responsive: false, // Disable responsive karena menggunakan scrollX
        scrollY: "500px", // Tinggi scroll untuk menampilkan ~10 baris data sekaligus
        scrollX: true, // Aktifkan scroll horizontal
        scrollCollapse: false, // Jangan collapse scroll jika data sedikit
        pageLength: 10000, // Memuat semua data sekaligus (jumlah besar untuk memuat semua)
        lengthChange: false, // Sembunyikan dropdown untuk mengubah jumlah data per halaman
        select: {
            style: 'single'
        },
        aaSorting: [],
        columnDefs: [{
            searchable: false,
            targets: [0]
        }],
        columns: [{
                "data": "id",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + ".";
                }
            },
            {
                data: "tgl_absen",
                render: function (data, type, row, meta) {
                    if (data == '' || data == null) {
                        return '-';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "name",
                render: function (data, type, row, meta) {
                    if (data == '' || data == null) {
                        return '-';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "no_hp",
                render: function (data, type, row, meta) {
                    if (data == '' || data == null) {
                        return '-';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "jam_masuk",
                render: function (data, type, row, meta) {
                    if (data == '' || data == null) {
                        return '-';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "jam_keluar",
                render: function (data, type, row, meta) {
                    if (data == '' || data == null) {
                        return '-';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "keterangan",
                render: function (data, type, row, meta) {
                    if (data == '' || data == null) {
                        return '-';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "status",
                render: function (data, type, row, meta) {
                    if (data == '' || data == null) {
                        return '-';
                    } else {
                        return data;
                    }
                }
            },
        ],
        createdRow: function (row, data, index) {
            $(row).attr("data-value", encodeURIComponent(JSON.stringify(data)));
            $("thead").css({
                "vertical-align": "middle",
                "text-align": "center",
            });
            $("td", row).css({
                "vertical-align": "middle",
                padding: "0.5em",
                cursor: "pointer",
            });
            $("td", row).first().css({
                width: "2%",
                "text-align": "center",
            });
            $("td", row).eq(2).css({
                "text-align": "center",
                "font-weight": "normal",
            });
            $("td", row).eq(3).css({
                "text-align": "center",
                "font-weight": "normal",
            });
            $("td", row).eq(4).css({
                "text-align": "center",
                "font-weight": "normal",
                width: "5%",
            });
            //Default
            $('#dv_table').show();
        },
        initComplete: function() {
            $('#dv_table').show();
            // Panggil callback jika ada, setelah DataTable selesai diinisialisasi
            if (typeof callback === 'function') {
                callback();
            }
        }
    })
    .on("select", function (e, dt, type, indexes) {
        var rowData = table.row(indexes).data();
        $("#btn-edit").removeClass("disabled");
        $("#btn-delete").removeClass("disabled");
        alert('1');
    })
    .on("deselect", function (e, dt, type, indexes) {
        $("#btn-edit").addClass("disabled");
        $("#btn-delete").addClass("disabled");
        alert('0');
    });

    // Handle row selection - modifikasi untuk mengabaikan row total dan grand total
    $('.basic-datatables tbody').off('click', 'tr').on('click', 'tr', function () {
        // Jangan pilih row total atau grand total
        if ($(this).hasClass('total-row') || $(this).hasClass('grand-total-row')) {
            return;
        }

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            $('#btn-ubah').addClass('disabled');
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            $('#btn-ubah').removeClass('disabled');
        }
    });
}

// Fungsi untuk membuat row total
function createTotalRow(divisi, totals) {
    console.log('Totals data:', totals);

    var totalRow = $('<tr class="total-row" style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6; border-bottom: 1px solid #dee2e6;">');

    totalRow.append('<td colspan="5" style="color: #6c757d; font-style: italic;">TOTAL '+ divisi +'</td>');
    totalRow.append('<td style="text-align: right; color: #28a745; font-weight: bold;">' + formatRupiah(totals.profit_holding) + '</td>');
    totalRow.append('<td style="text-align: right; color: #28a745; font-weight: bold;">' + formatRupiah(totals.profit_leader) + '</td>');
    totalRow.append('<td style="text-align: right; color: #28a745; font-weight: bold;">' + formatRupiah(totals.profit_dirutama) + '</td>');
    totalRow.append('<td style="text-align: right; color: #28a745; font-weight: bold;">' + formatRupiah(totals.profit_sim) + '</td>');
    totalRow.append('<td style="text-align: right; color: #28a745; font-weight: bold;">' + formatRupiah(totals.profit_keuangan) + '</td>');
    totalRow.append('<td style="text-align: right; color: #28a745; font-weight: bold;">' + formatRupiah(totals.total_profit) + '</td>');
    totalRow.append('<td style="text-align: center; color: #6c757d; font-style: italic;">-</td>');

    return totalRow;
}

// Fungsi untuk membuat grand total row
function createGrandTotalRow(grandTotal) {
    console.log('Grand Total data:', grandTotal);

    var grandTotalRow = $('<tr class="grand-total-row" style="background-color: #e3f2fd; font-weight: bold; border-top: 3px solid #2196f3; border-bottom: 2px solid #2196f3;">');

    grandTotalRow.append('<td colspan="5" style="color: #1976d2; font-style: italic; font-size: 14px; font-weight: bold;">GRAND TOTAL</td>');
    grandTotalRow.append('<td style="text-align: right; color: #1976d2; font-weight: bold; font-size: 14px;">' + formatRupiah(grandTotal.profit_holding) + '</td>');
    grandTotalRow.append('<td style="text-align: right; color: #1976d2; font-weight: bold; font-size: 14px;">' + formatRupiah('-') + '</td>');
    grandTotalRow.append('<td style="text-align: right; color: #1976d2; font-weight: bold; font-size: 14px;">' + formatRupiah(grandTotal.profit_dirutama) + '</td>');
    grandTotalRow.append('<td style="text-align: right; color: #1976d2; font-weight: bold; font-size: 14px;">' + formatRupiah(grandTotal.profit_sim) + '</td>');
    grandTotalRow.append('<td style="text-align: right; color: #1976d2; font-weight: bold; font-size: 14px;">' + formatRupiah(grandTotal.profit_keuangan) + '</td>');
    grandTotalRow.append('<td style="text-align: right; color: #1976d2; font-weight: bold; font-size: 14px;">' + formatRupiah(grandTotal.total_profit) + '</td>');
    grandTotalRow.append('<td style="text-align: center; color: #1976d2; font-style: italic; font-size: 14px;">-</td>');

    return grandTotalRow;
}

// Tambahkan fungsi helper untuk handle modal
function closeModal() {
    $('#formModal').modal('hide');
    $('#formModal').hide();
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
}

// Tambahkan fungsi helper di bagian atas script
function showNotification(type, message) {
    Swal.fire({
        title: type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        icon: type, // 'success', 'error', 'warning', 'info'
        confirmButtonText: 'OK'
    });
}

function collectionS2Search() {
    $('select[name=cmb_nip]').select2({
        dropdownParent: $('#formFilter'),
        allowClear: true,
        width: '72.5%',
        placeholder: '',
        ajax: {
            url: "{{ url('/pr_wapu/getSales') }}",
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data.data, function (item) {
                        return {
                            text: item.nip,
                            name: item.name,
                            id: item.nip
                        }
                    }),
                    pagination: {
                        more: false
                    }
                };
            },
            cache: true
        }
    });

     // Event handler for when kategori changes
     $('select[name=cmb_nip]').on('select2:select', function (e) {
        var data = e.params.data;

        $('#name').val(data.name);
    });
}

</script>
  @endpush

@endsection
