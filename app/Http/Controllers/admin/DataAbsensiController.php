<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\User;
use App\Models\VwDataabsen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

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

    public function datatable(Request $request)
    {
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);

        // Frontend mengirim "cmb_karyawan"; fallback untuk nama lama (jika ada)
        $idUser = $request->get('cmb_karyawan') ?? $request->get('cmb_nip');
        $periodeStart = $request->get('periode_start');

        $baseQuery = VwDataabsen::query();
        $query = VwDataabsen::query()->orderBy('tgl_absen', 'desc');

        // Filter berdasarkan bulan (periode_start)
        if ($periodeStart) {
            // Support "YYYY-MM" (datepicker sekarang) dan "YYYYMM" (kalau ada legacy)
            if (preg_match('/^\d{6}$/', (string) $periodeStart)) {
                $periodeRangeStart = Carbon::createFromFormat('Ym', $periodeStart)->startOfMonth();
                $periodeRangeEnd = Carbon::createFromFormat('Ym', $periodeStart)->endOfMonth();
            } elseif (preg_match('/^\d{4}-\d{2}$/', (string) $periodeStart)) {
                $periodeRangeStart = Carbon::createFromFormat('Y-m', $periodeStart)->startOfMonth();
                $periodeRangeEnd = Carbon::createFromFormat('Y-m', $periodeStart)->endOfMonth();
            } else {
                $periodeRangeStart = Carbon::parse($periodeStart)->startOfMonth();
                $periodeRangeEnd = Carbon::parse($periodeStart)->endOfMonth();
            }

            $query->whereBetween('tgl_absen', [
                $periodeRangeStart->toDateString(),
                $periodeRangeEnd->toDateString(),
            ]);
        }

        // Filter berdasarkan karyawan (id user)
        if (!empty($idUser)) {
            $userIdColumns = ['user_id', 'id_user', 'karyawan_id', 'id_karyawan'];
            $matchedColumn = null;
            foreach ($userIdColumns as $col) {
                if (Schema::hasColumn('vw_data_absensi', $col)) {
                    $matchedColumn = $col;
                    break;
                }
            }

            // Kalau kolom id user tidak ditemukan, jangan hard-fail.
            // Minimal tetap bisa filter via nama kalau view hanya punya kolom "name".
            if ($matchedColumn) {
                $query->where($matchedColumn, $idUser);
            } elseif (Schema::hasColumn('vw_data_absensi', 'id') && $request->filled('cmb_nip')) {
                // Fallback sangat konservatif, biar tidak merusak instalasi lama.
                // (Jika ternyata cmb_nip dulu berisi id absen, filter tetap bekerja.)
                $query->where('id', $idUser);
            }
        }

        $recordsTotal = $baseQuery->count();
        $recordsFiltered = $query->count();

        // Apply pagination
        $results = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
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

    public function getKaryawan()
    {
        $result = User::query()
            ->where('role', 'staff')
            ->when(request('q'), function ($query, $term) {
                $query->where('name', 'like', '%' . $term . '%');
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'error' => 0,
            'message' => 'Success',
            'data'=> $result
        ]);
    }




}
