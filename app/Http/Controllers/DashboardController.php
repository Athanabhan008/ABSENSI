<?php

namespace App\Http\Controllers;

use App\Models\Izinsakit;
use App\Models\Vwcountcutitoday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Vwcountsakittoday;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini =date("Y-m-d");
        $rekappresensi = DB::table('absens')
        ->selectRaw('COUNT(id_user) as jmlhadir, SUM(IF(jam_masuk > "08:05",1,0)) as jmlterlambat')
        ->where('tgl_absen', $hariini)
        ->first();

        $rekapcuti = Vwcountcutitoday::all();
        $rekapsakit = Vwcountsakittoday::all();

        return view('admin.index', [
            'rekappresensi' => $rekappresensi,
            'rekapcuti' => $rekapcuti,
            'rekapsakit' => $rekapsakit,

        ]);
    }

    public function getabsensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $absensi = DB::table('absens')
        ->select('absens.*', 'name')
        ->join('users', 'absens.id_user', '=', 'users.id')
        ->where('tgl_absen', $tanggal)
        ->get();

        return view('admin.getabsensi', [
            'absensi' => $absensi
        ]);
    }

    public function showmap(Request $request)
    {
        $id = $request->id;
        $absensi = DB::table('absens')->where('id', $id)->first();
        return view('admin.getmap',compact('absensi'));
    }

}
