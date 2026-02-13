@extends('layouts.template_absen')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">

<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Form Izin/Sakit</div>
    <div class="right"></div>
</div>
 <!-- App Header -->

 <form action="/izin_sakit/store" method="POST" enctype="multipart/form-data" style="margin-top: 70px">
    @csrf
    <div class="col">
        <div class="form-group boxed">
            <div class="input-wrapper">
                <select class="form-control form-control-lg" name="status" id="inputGroupSelect01"
                    onchange="document.getElementById('fileUpload1').style.display = this.value === 'sakit' ? 'block' : 'none';"
                    required>
                    <option value="" selected disabled>Pilih Status...</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                </select>
            </div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <input type="text" name="tgl_pengajuan" id="periode_start" class="form-control form-control-lg pl-3 datepicker-start" placeholder="Pilih tanggal Awal" autocomplete="off" value="{{ $periode_start ?? '' }}">
            </div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <input type="text" name="tgl_pengajuan_akhir" id="periode_end" class="form-control form-control-lg pl-3 datepicker-end" placeholder="Pilih tanggal Akhir" autocomplete="off" value="{{ $periode_end ?? '' }}">
            </div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <input type="number" id="date_range_result" name="total_hari" class="form-control form-control-lg" placeholder="Total hari" readonly>
            </div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <div class="input-group">
                    <textarea class="form-control" aria-label="With textarea" name="keterangan" placeholder="Keterangan"></textarea>
                  </div>
            </div>
        </div>
        <div class="custom-file-upload" id="fileUpload1" style="display: none;">
            <input type="file" name="foto_surat" id="fileuploadInput" accept=".png, .jpg, .jpeg">
            <label for="fileuploadInput">
                <span>
                    <strong>
                        <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
                        <i>Upload surat dokter disini</i>
                    </strong>
                </span>
            </label>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <button type="submit" class="btn btn-primary btn-block">
                    <ion-icon name="save-outline"></ion-icon>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="../../admin/assets/js/plugins/bootstrap-datepicker.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>

 <script>

        // Date range (start - end) using bootstrap-datepicker
        const $start = $('#periode_start');
        const $end = $('#periode_end');
        const $result = $('#date_range_result');

        function parseYmdToDate(ymd) {
            // ymd: "yyyy-mm-dd"
            if (!ymd || typeof ymd !== 'string') return null;
            const d = new Date(`${ymd}T00:00:00`);
            return Number.isNaN(d.getTime()) ? null : d;
        }

            function formatRangeText(startYmd, endYmd) {
                const start = parseYmdToDate(startYmd);
                const end = parseYmdToDate(endYmd);

                if (!start || !end) return '';

                const diffMs = end.getTime() - start.getTime();
                const diffDays = Math.floor(diffMs / (24 * 60 * 60 * 1000)) + 1;

                if (diffDays <= 0) return '';

                return diffDays;
            }

            function updateResult() {
                $result.val(formatRangeText($start.val(), $end.val()));
            }

        $start.datepicker({
            format: "yyyy-mm-dd",
            minViewMode: "days",
            startView: "month",
            autoclose: true
        }).on('changeDate change', function () {
            const startVal = $start.val();
            const startDate = parseYmdToDate(startVal);
            if (startDate) {
                // end tidak boleh < start
                $end.datepicker('setStartDate', startDate);
                // kalau end sudah terlanjur < start, kosongkan
                const endDate = parseYmdToDate($end.val());
                if (endDate && endDate.getTime() < startDate.getTime()) {
                    $end.val('');
                }
            } else {
                $end.datepicker('setStartDate', null);
            }
            updateResult();
        });

        $end.datepicker({
            format: "yyyy-mm-dd",
            minViewMode: "days",
            startView: "month",
            autoclose: true
        }).on('changeDate change', function () {
            const endVal = $end.val();
            const endDate = parseYmdToDate(endVal);
            if (endDate) {
                // start tidak boleh > end
                $start.datepicker('setEndDate', endDate);
                const startDate = parseYmdToDate($start.val());
                if (startDate && startDate.getTime() > endDate.getTime()) {
                    $start.val('');
                }
            } else {
                $start.datepicker('setEndDate', null);
            }
            updateResult();
        });

        // initial render (kalau ada value dari server)
        updateResult();

     </script>
 @endpush

 @endsection
