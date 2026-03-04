<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $id_user = Auth::guard('web')->user()->id;
        $absenHariIni = DB::table('absens')
            ->where('tgl_absen', $hariini)
            ->where('id_user', $id_user)
            ->first();

        // Jika sudah absen masuk DAN absen pulang (jam_keluar terisi), blok akses halaman absen
        if ($absenHariIni && !empty($absenHariIni->jam_keluar)) {
            return redirect('/dashboard')->with('error', 'Anda sudah melakukan absen masuk dan absen pulang hari ini.');
        }

        $cek = $absenHariIni ? 1 : 0;
        return view('absen.create', [
            'cek' => $cek,
            'active' => 'absen'
        ]);
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $id_user = auth()->user()->id;
        $keterangan = trim($request->keterangan ?? '');
        $tgl_absen = date("Y-m-d");
        $jam_masuk = date("H:i:s");
        $latitudekantor = -6.919080053798793;
        $longitudekantor = 107.7153742206726;
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak  = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $cek = DB::table('absens')
            ->where('tgl_absen', $tgl_absen)
            ->where('id_user', $id_user)
            ->count();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }

        // aturan jam dan status
        $batasMasuk  = "08:10:00";
        $batasPulang = "17:00:00";
        $isTelatMasuk = ($cek == 0 && $jam_masuk > $batasMasuk);
        $isPulangCepat = ($cek > 0 && $jam_masuk < $batasPulang);
        $diLuarRadius = ($radius > 10);

        // kombinasi aturan: jika salah satu kondisi ini terjadi, keterangan wajib diisi
        if (($isTelatMasuk || $isPulangCepat || $diLuarRadius) && $keterangan === '') {
            echo "error|Anda Wajib mengisi kolom keterangan Karna anda telat/berada diluar radius";
            return;
        }

        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $id_user."-".$tgl_absen . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;
        $status = $isTelatMasuk ? 'telat' : 'hadir';

        // hitung jumlah telat sebelumnya
        $totalTelat = DB::table('absens')
            ->where('id_user', $id_user)
            ->where('status', 'telat')
            ->count();

        // tentukan status approve
        if ($status == 'hadir') {
            $statusApprove = 'approve';
        } else {
            // jika telat dan sudah 4 kali atau lebih
            if (($totalTelat + 1) >= 4) {
                $statusApprove = 0;
            } else {
                $statusApprove = 1;
            }
        }

        $data = [
            'id_user'         => $id_user,
            'tgl_absen'       => $tgl_absen,
            'jam_masuk'       => $jam_masuk,
            'lokasi_masuk'    => $lokasi,
            'foto_masuk'      => $fileName,
            'status'          => $status,
            'status_approve'  => $statusApprove,
            'keterangan'      => $keterangan,
        ];

        if ($cek > 0) {
        $data_pulang = [
            'jam_keluar'    => $jam_masuk,
            'lokasi_keluar' => $lokasi,
            'foto_keluar'   => $fileName,
            'keterangan'    => $keterangan,
        ];
        $update = DB::table('absens')->where('tgl_absen', $tgl_absen)->where('id_user', $id_user)->update($data_pulang);
        if ($update) {
            echo "success|Terima kasih anda sudah melakukan absen pulang|out";
            Storage::put($file, $image_base64);
        } else {
            echo "error|Maaf gagal absen|out";

        }

        }   else {

            $simpan = DB::table('absens')->insert($data);
            if ($simpan){
                echo "success|Terima kasih anda sudah melakukan absen masuk|in";
                Storage::put($file, $image_base64);
            } else {
                echo "error|Maaf gagal absen|in";
            }
        }

    }

    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function histori(Request $request)
    {
        $id_user = Auth::guard('web')->user()->id;
        $periode_start = $request->input('periode_start');

        $query = DB::table('absens')
            ->where('id_user', $id_user)
            ->orderBy('tgl_absen', 'desc');

        if ($periode_start) {
            // Parse periode_start (format: YYYY-MM)
            $dateParts = explode('-', $periode_start);
            if (count($dateParts) == 2) {
                $year = $dateParts[0];
                $month = $dateParts[1];

                $query->whereRaw('YEAR(tgl_absen) = ?', [$year])
                      ->whereRaw('MONTH(tgl_absen) = ?', [$month]);
            }
        }

        $absensi = $query->get();

        return view('histori.index', [
            'absensi' => $absensi,
            'periode_start' => $periode_start,
            'active' => 'histori'
        ]);
    }
}
