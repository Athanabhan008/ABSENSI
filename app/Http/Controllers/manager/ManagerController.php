<?php

namespace App\Http\Controllers\manager;

use App\Models\Vwcountsakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Vwcountcuti;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function index()
    {

        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");
        $id = Auth::guard('web')->user()->id;
        $absensihariini = DB::table('absens')->where('id_user', $id)->where('tgl_absen', $hariini)->first();
        $historibulanini = DB::table('absens')->whereRaw('MONTH(tgl_absen)="' . $bulanini . '"')
            ->where('id_user', $id)
            ->whereRaw('YEAR(tgl_absen)="' . $tahunini . '"')
            ->orderBy('tgl_absen')
            ->get();

            $rekappresensi = DB::table('absens')
            ->selectRaw('COUNT(id_user) as jmlhadir, SUM(IF(jam_masuk > "08:05",1,0)) as jmlterlambat')
            ->where('id_user', $id)
            ->whereRaw('MONTH(tgl_absen)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_absen)="' . $tahunini . '"')
            ->first();

            $leaderboard = db::table('absens')
            ->join('users', 'absens.id_user', '=', 'users.id')
            ->where('tgl_absen', $hariini)
            ->orderBy('jam_masuk')
            ->get();

            $jmlcuti = Cuti::where('id_user', $id)
            ->where('status_approve', 1)
            ->sum('total_hari');

        $rekapsakit = Vwcountsakit::where('id_user', $id)->get();

        $jatah_cuti = Auth::guard('web')->user()->jatah_cuti ?? 0;
        $sisa_cuti = max(0, $jatah_cuti - $jmlcuti);

        $bulan_nama_carbon = Carbon::now()->format('F');
        $tahunSekarang = now()->format('Y');

        return view('manager.dashboard', [
            "active" => 'manager',
            "absensihariini" => $absensihariini,
            "historibulanini" => $historibulanini,
            "bulan_nama_carbon" => $bulan_nama_carbon,
            "tahunSekarang" => $tahunSekarang,
            "rekappresensi" => $rekappresensi,
            "leaderboard" => $leaderboard,
            "jmlcuti" => $jmlcuti,
            "rekapsakit" => $rekapsakit,
            "jatah_cuti" => $jatah_cuti,
            "sisa_cuti" => $sisa_cuti,
            "jmlcuti" => $jmlcuti,
            "sisa_cuti" => $sisa_cuti,
        ]);
    }
}
