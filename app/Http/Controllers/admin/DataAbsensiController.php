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

    public function datatable()
    {
        $draw = request()->get('draw');
        $start = request()->get('start');
        $length = request()->get('length');
        $id_user = request()->get('cmb_nip');
        $periode_start = request()->get('periode_start');
        $cmb_sales = request()->get('cmb_sales');

        $user = auth()->user();
        $query = VwDataabsen::query()->orderBy('tgl_absen', 'desc');

        // Filter berdasarkan bulan (periode_start)
        if ($periode_start) {
            // Format dari frontend: "yyyy-mm" (contoh: "2024-01")
            $year = substr($periode_start, 0, 4);
            $month = substr($periode_start, 5, 2);

            $startDate = \Carbon\Carbon::createFromFormat('Y-m', "$year-$month")->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', "$year-$month")->endOfMonth();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $total = $query->count();

        // Apply pagination
        $results = $query->offset($start)
                        ->limit($length)
                        ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $results
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
