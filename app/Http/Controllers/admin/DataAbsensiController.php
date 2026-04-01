<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\VwDataabsen;

class DataAbsensiController extends Controller
{
    public function index()
    {
        $data_absensi = VwDataabsen::all();
        return view('data_absensi.index', [
            'data_absensi' => $data_absensi,
            "active" => 'data_absensi'
        ]);
    }

    public function approve(Request $request, $id)
    {
        $cuti                                  = Absen::find($id);
        $cuti->status_approve                  = $request->status_approve;
        $cuti->save();

        return back();
    }





}
