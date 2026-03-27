<?php

namespace App\Http\Controllers;

use App\Models\Izinsakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = User::all();
        return view('karyawan.index', [
            'karyawan' => $karyawan,
            "active" => 'karyawan'
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
        $query = User::query()->orderBy('created_at', 'desc');

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

    public function create(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|unique:users',
                'no_hp' => 'required',
                'password' => 'required',
                'role' => 'required',
                'tgl_masuk' => 'required'
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required.unique' => 'Email wajib diisi',
                'no_hp.required' => 'Nomor HP Wajib diisi',
                'password.required' => 'Password Wajib diisi',
                'role.required' => 'Role Wajib diisi',
                'tgl_masuk.required' => 'Tanggal Masuk Wajib diisi'
            ]);

            $karyawan = new User();
            $karyawan->name         = $request->name;
            $karyawan->email        = $request->email;
            $karyawan->no_hp        = $request->no_hp;
            $karyawan->password     = $request->password;
            $karyawan->role         = $request->role;
            $karyawan->tgl_masuk    = $request->tgl_masuk;
            $karyawan->updated_at   = null;
            $karyawan->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $karyawan
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = User::whereId($id)->first();
        return view('karyawan.edit', [
            "active" => 'karyawan'
        ])->with('user', $user);
    }
    public function update(Request $request, $id)
    {
        $user                                  = User::find($id);
        $user->name                            = $request->name;
        $user->email                           = $request->email;
        $user->no_hp                           = $request->no_hp;
        $user->role                            = $request->role;
        $user->save();

        return redirect('/karyawan');
    }

    public function edit_password($id)
    {
        $user = User::whereId($id)->first();
        return view('karyawan.edit_password', [
             "active" => 'karyawan'
        ])->with('user', $user);
    }

    public function update_password(Request $request, $id)
    {
        $user                                 = User::find($id);
        $user->password                       = password_hash($request->password, PASSWORD_DEFAULT);
        $user->save();

        return redirect('/karyawan');
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
