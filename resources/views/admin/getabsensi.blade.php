
<?php

function selisih($jam_masuk, $jam_keluar)
{
    list($h, $m, $s) = explode(":", $jam_masuk);
    $dtAwal = mktime($h, $m, $s, "1", "1", "1");
    list($h, $m, $s) = explode(":", $jam_keluar);
    $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
    $dtSelisih = $dtAkhir - $dtAwal;
    $totalmenit = $dtSelisih / 60;
    $jam = explode(".", $totalmenit / 60);
    $sisamenit = ($totalmenit / 60) - $jam[0];
    $sisamenit2 = $sisamenit * 60;
    $jml_jam = $jam[0];
    return $jml_jam . ":" . round($sisamenit2);
}
?>

<?php
use Illuminate\Support\Facades\Storage;
?>
@foreach ($absensi as $item)
<tr>
    <td style="text-align: center">{{ $loop->iteration }}</td>
    <td style="text-align: center">{{ $item->name }}</td>
    <td style="text-align: center">{{ $item->jam_masuk }}</td>
    <td style="text-align: center">
        <?php
             $pathMasuk = Storage::url('uploads/absensi/' . $item->foto_masuk);
        ?>
          <a href="javascript:void(0)" class="preview-link" onclick="openPreview('{{ asset('storage/uploads/absensi/'.$item->foto_masuk) }}')">
            <img src="{{ asset('storage/uploads/absensi/'.$item->foto_masuk) }}" class="preview-thumb">
        </a>
    </td>
    <td style="text-align: center">{!! $item->jam_keluar != null ? $item->jam_keluar : '<span class="badge bg-danger">Belum Absen</span>' !!}</td>
    <td style="text-align: center">
        @if ($item->jam_keluar != null)
        <?php
             $pathKeluar = Storage::url('uploads/absensi/' . $item->foto_keluar);
        ?>
         <a href="javascript:void(0)" class="preview-link" onclick="openPreview('{{ asset('storage/uploads/absensi/'.$item->foto_keluar) }}')">
            <img src="{{ asset('storage/uploads/absensi/'.$item->foto_keluar) }}" class="preview-thumb">
        </a>
        @else
        <i class="fa-solid fa-image fa-3x" ></i>
        @endif

    </td>
    <td style="text-align: center">
        @if($item->jam_masuk > '08:05:00')
        <?php
        $jamterlambat = selisih('08:05:00', $item->jam_masuk);
        ?>
        <span class="badge bg-danger">Terlambat {{ $jamterlambat }}</span>
        @else
        <span class="badge bg-success">Tepat Waktu</span>
        @endif

    </td>
    <td style="text-align: center;">
        <a class="btn btn-primary showmap" id="{{ $item->id }}">
            <i class="fa-solid fa-map fa-1x" style="color: white;"></i>
        </a>
    </td>
</tr>

@endforeach

<script>
    $(function(){
        $(".showmap").click(function(e) {
            var id = $(this).attr("id");
            $.ajax({
                type:'POST',
                url:'/showmap',
                data: {
                    _token: "{{ csrf_token() }}",
                    id:id
                },
                cache:false,
                success:function(respond){
                    $("#loadmap").html(respond);
                }
            });
            $("#modal-map").modal("show");
        });
    });
</script>
