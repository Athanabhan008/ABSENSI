<?php

namespace App\Http\Controllers\admin;

use App\Models\Izinsakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Vwcuti;

class CutiController extends Controller
{
    public function index()
    {
        $cuti = Vwcuti::all();
        return view('cuti.index', [
            'cuti' => $cuti,
            "active" => 'cuti'
        ]);
    }

    public function datatable()
    {
        $draw = request()->get('draw');
        $start = request()->get('start');
        $length = request()->get('length');
        $periode_start = request()->get('periode_start');


        $query = Vwcuti::query()->orderBy('created_at', 'desc');

        // Filter berdasarkan bulan (periode_start)
        if ($periode_start) {
            // Format dari frontend: "yyyy-mm" (contoh: "2024-01")
            $year = substr($periode_start, 0, 4);
            $month = substr($periode_start, 5, 2);

            $startDate = \Carbon\Carbon::createFromFormat('Y-m', "$year-$month")->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', "$year-$month")->endOfMonth();

            $query->whereBetween('created_at', [$startDate->toDateString(), $endDate->toDateString()]);
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
        $cuti                                  = Cuti::find($id);
        $cuti->status_approve                  = $request->status_approve;
        $cuti->save();

        return back();
    }





}
