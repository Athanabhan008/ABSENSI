<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.edit', [
            'user' => $user,
            'active' => 'profile'
        ]);
    }

    public function updateprofile(Request $request)
    {
        $user = auth()->user();
        // $user = Auth::guard('web')->user()->id;
        $name = $request->name;
        $no_hp = $request->no_hp;
        $email = $request->email;

        // Hash password only when provided to avoid overriding with a hashed empty string.
        $password = $request->filled('password') ? Hash::make($request->password) : null;

        $data = [
            'name' => $name,
            'no_hp' => $no_hp,
            'email' => $email,
        ];

        if ($password) {
            $data['password'] = $password;
        }

        $update = DB::table('users')->where('id', $user->id)->update($data);
        if($update){
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['errors' => 'Data Gagal Diupdate']);
        }



    }

}
