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

            $query->whereBetween('tgl_absen', [$startDate->toDateString(), $endDate->toDateString()]);
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
        $validated = $request->validate([
            'status_approve' => 'required|in:0,1,2',
        ]);

        $absen = Absen::findOrFail($id);
        $absen->status_approve = $validated['status_approve'];
        $absen->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Data approval berhasil disimpan.',
            ]);
        }

        return back()->with('success', 'Data approval berhasil disimpan.');
    }





}
