<?php

namespace App\Http\Controllers;

use App\Models\Sakit;
use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Vwcutisakit;
use App\Models\Vwizinsakit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class CutiSakitController extends Controller
{
    public function index()
    {
        $dataizin = Vwizinsakit::where('id_user', Auth::id())->get();
        return view('izin.index', [
            'dataizin' => $dataizin,
            'active'  => 'izin'
        ]);
    }

    public function create()
    {
        return view('izin.create', [
            'active' => 'izin'
        ]);
    }
    public function store(Request $request)
{
    $request->validate([
        'status' => 'required',
        'tgl_pengajuan' => 'required|date',
        'tgl_pengajuan_akhir' => 'required|date',
        'total_hari' => 'required|numeric',
        'keterangan' => 'required',
        'foto_surat' => $request->status == 'sakit'
            ? 'required|image|mimes:jpeg,png,jpg|max:2048'
            : 'nullable'
    ]);

    $user = auth()->user();

    // =========================
    // JIKA STATUS = IZIN
    // =========================
    if ($request->status == 'izin') {

        $izin = new Cuti();
        $izin->id_user = $user->id;
        $izin->tgl_pengajuan = $request->tgl_pengajuan;
        $izin->tgl_pengajuan_akhir = $request->tgl_pengajuan_akhir;
        $izin->total_hari = $request->total_hari;
        $izin->status_approve = 0;
        $izin->keterangan = $request->keterangan;
        $izin->save();

    }
    // =========================
    // JIKA STATUS = SAKIT
    // =========================
    else if ($request->status == 'sakit') {

        $sakit = new Sakit();
        $sakit->id_user = $user->id;
        $sakit->tgl_pengajuan = $request->tgl_pengajuan;
        $sakit->tgl_pengajuan_akhir = $request->tgl_pengajuan_akhir;
        $sakit->total_hari = $request->total_hari;
        $sakit->status_approve = 0;
        $sakit->keterangan = $request->keterangan;

        // upload surat dokter
        if ($request->hasFile('foto_surat')) {

            $file = $request->file('foto_surat');

            $imageName = time().'_'.Str::random(10).'.jpg';

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file);

            // resize biar ringan
            $image->scale(width: 1000);

            $path = storage_path('app/public/uploads/surat_sakit/'.$imageName);

            $quality = 90;

            do {
                $image->toJpeg($quality)->save($path);
                $size = filesize($path);
                $quality -= 5;
            } while ($size > 50000 && $quality > 10);

            $sakit->foto_surat = $imageName;
        }

        $sakit->save();
    }

    return redirect('/izin_sakit')->with('success', 'Data Berhasil Disimpan');
}


}
