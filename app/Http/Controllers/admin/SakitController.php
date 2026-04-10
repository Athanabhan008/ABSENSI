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
use App\Models\Sakit;
use App\Models\Vwizinsakit;
use App\Models\Vwsakit;

class SakitController extends Controller
{

    public function index()
    {
        $sakit = Vwsakit::all();
        return view('sakit.index', [
            'sakit' => $sakit,
            "active" => 'sakit'
        ]);
    }

    public function datatable()
    {
        $draw = request()->get('draw');
        $start = request()->get('start');
        $length = request()->get('length');
        $periode_start = request()->get('periode_start');


        $query = Vwsakit::query()->orderBy('created_at', 'desc');

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
        $validated = $request->validate([
            'status_approve' => 'required|in:0,1,2',
        ]);

        $absen = Sakit::findOrFail($id);
        $absen->status_approve = $validated['status_approve'];
        $absen->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Approval berhasil Dirubah.',
            ]);
        }

        return back()->with('success', 'Approval berhasil dirubah.');
    }

}
