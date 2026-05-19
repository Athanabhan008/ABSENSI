<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
    $id_user = $user->id;

    $keteranganMasukInput = trim($request->keterangan_masuk ?? '');
    $keteranganPulangInput = trim($request->keterangan_pulang ?? '');

    foreach ([$keteranganMasukInput, $keteranganPulangInput] as $inputKeterangan) {
        if ($inputKeterangan !== '' && !$this->isKeteranganAlphanumeric($inputKeterangan)) {
            echo "error|Keterangan hanya boleh berisi huruf, angka, dan spasi";
            return;
        }
    }

    $keterangan_masuk = $keteranganMasukInput ?: $keteranganPulangInput;
    $tgl_absen = date("Y-m-d");
    $jam_masuk = date("H:i:s");

    $latitudekantor = -6.919080053798793;
    $longitudekantor = 107.7153742206726;

    $lokasi = $request->lokasi;

    if (!$lokasi) {
        echo "error|Lokasi tidak ditemukan";
        return;
    }

    $lokasiuser = explode(",", $lokasi);
    if (count($lokasiuser) < 2) {
        echo "error|Format lokasi tidak valid";
        return;
    }

    $latitudeuser = $lokasiuser[0];
    $longitudeuser = $lokasiuser[1];

    $jarak  = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
    $radius = round($jarak["meters"]);

    $existing = DB::table('absens')
        ->where('tgl_absen', $tgl_absen)
        ->where('id_user', $id_user)
        ->first();

    if ($existing && $existing->jam_masuk && !$existing->jam_keluar) {
        $ket = "out";
    } else {
        $ket = "in";
    }

    // aturan jam
    $batasMasuk  = "08:05:00";
    $batasPulang = "17:00:00";

    $isTelatMasuk = ($ket == "in" && $jam_masuk > $batasMasuk);
    $isPulangCepat = ($ket == "out" && $jam_masuk < $batasPulang);
    $diLuarRadius = ($radius > 20);

    // wajib isi keterangan
    if (($isTelatMasuk || $isPulangCepat || $diLuarRadius) && $keterangan_masuk === '') {
        echo "error|Anda wajib mengisi keterangan karena telat / di luar radius";
        return;
    }

    $image = $request->image;
    if (!$image) {
        echo "error|Gambar tidak ditemukan";
        return;
    }

    $folderPath = "public/uploads/absensi/";
    $formatName = $id_user . "-" . $tgl_absen . "-" . $ket;

    $image_parts = explode(";base64", $image);
    if (count($image_parts) < 2) {
        echo "error|Format gambar tidak valid";
        return;
    }

    $image_base64 = base64_decode($image_parts[1]);
    $fileName = $formatName . ".png";
    $file = $folderPath . $fileName;

    $status = $isTelatMasuk ? 'telat' : 'hadir';

    // Hitung telat hanya dalam bulan kalender berjalan (reset tiap ganti bulan)
    $refTanggal = Carbon::parse($tgl_absen);
    $awalBulan = $refTanggal->copy()->startOfMonth()->toDateString();
    $akhirBulan = $refTanggal->copy()->endOfMonth()->toDateString();

    $totalTelat = DB::table('absens')
        ->where('id_user', $id_user)
        ->where('jam_masuk', '>', $batasMasuk)
        ->whereBetween('tgl_absen', [$awalBulan, $akhirBulan])
        ->count();

    // status approve: need approve (0) jika telat dalam sebulan sudah > 3 kali
    if ($status == 'hadir') {
        $statusApprove = 1;
    } else {
        $statusApprove = (($totalTelat + 1) > 3) ? 0 : 1;
    }

    if ($existing) {

        // ABSEN PULANG
        if ($existing->jam_masuk && !$existing->jam_keluar) {

            $update = DB::table('absens')
                ->where('id', $existing->id)
                ->update([
                    'jam_keluar'           => $jam_masuk,
                    'lokasi_keluar'        => $lokasi,
                    'foto_keluar'          => $fileName,
                    'keterangan_pulang'    => $keterangan_masuk,
                ]);

            if ($update) {
                echo "success|Terima kasih anda sudah melakukan absen pulang|out";
                Storage::put($file, $image_base64);
            } else {
                echo "error|Gagal absen pulang|out";
            }

        } else {

            $update = DB::table('absens')
                ->where('id', $existing->id)
                ->update([
                    'jam_masuk'            => $jam_masuk,
                    'lokasi_masuk'         => $lokasi,
                    'foto_masuk'           => $fileName,
                    'status'               => $status,
                    'status_approve'       => $statusApprove,
                    'keterangan_masuk'     => $keterangan_masuk,
                ]);

            if ($update) {
                echo "success|Terima kasih anda sudah melakukan absen masuk|in";
                Storage::put($file, $image_base64);
            } else {
                echo "error|Gagal update absen|in";
            }
        }

    } else {

        $simpan = DB::table('absens')->insert([
            'id_user'               => $id_user,
            'tgl_absen'             => $tgl_absen,
            'jam_masuk'             => $jam_masuk,
            'lokasi_masuk'          => $lokasi,
            'foto_masuk'            => $fileName,
            'status'                => $status,
            'status_approve'        => $statusApprove,
            'keterangan_masuk'      => $keterangan_masuk,
        ]);

        if ($simpan){
            echo "success|Terima kasih anda sudah melakukan absen masuk|in";
            Storage::put($file, $image_base64);
        } else {
            echo "error|Gagal absen|in";
        }
    }
}

    private function isKeteranganAlphanumeric(string $keterangan): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\s]+$/u', $keterangan);
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
