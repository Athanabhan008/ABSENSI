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

    public function create()
    {
        return view('karyawan.create', [
             "active" => 'karyawan'
        ]);
    }

    public function store(Request $request)
    {
        $user                       = new User();
        $user->name                 = $request->name;
        $user->email                = $request->email;
        $user->no_hp                = $request->no_hp;
        $user->password             = password_hash($request->password, PASSWORD_DEFAULT);
        $user->role                 = $request->role;
        $user->tgl_masuk            = $request->tgl_masuk;
        $user->save();
        return redirect('/karyawan')->with('success', 'Data Berhasil Disimpan');
    }

    public function edit($id)
    {
        $user = User::whereId($id)->first();
        return view('karyawan.edit')->with('user', $user);
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
        return view('karyawan.edit_password')->with('user', $user);
    }
    public function update_password(Request $request, $id)
    {
        $user                                 = User::find($id);
        $user->password                       = password_hash($request->password, PASSWORD_DEFAULT);
        $user->save();

        return redirect('/karyawan');
    }

}
