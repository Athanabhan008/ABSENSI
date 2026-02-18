<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesiController extends Controller
{
    public function index()
    {
        return view('login.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required'
        ],[
            'name.required' => 'name Wajid Diisi',
            'password.required' => 'Password Wajid Diisi',
        ]);

        $infologin = [
            'name' => $request->name,
            'password' => $request->password,
        ];
        if(Auth::attempt($infologin)){
            if (Auth::user()->role == 'superadmin'){
                return redirect('/dashboard');

            }elseif (Auth::user()->role == 'staff'){
                return redirect('/dashboard');

            }elseif (Auth::user()->role == 'admin'){
                return redirect('/dashboard');

            }

        }else{
            return redirect('')->withErrors('Username Dan Password Yang Dimasukkan Tidak Sesuai')->withInput();
        }
    }
    public function logout()
    {
        Auth::logout();
        return redirect('');
    }


    public function error()
    {
        return view('errors.error_login');
    }


        public function index_admin()
    {
        return view('login.login_admin');
    }
    public function login_admin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required'
        ],[
            'name.required' => 'name Wajid Diisi',
            'password.required' => 'Password Wajid Diisi',
        ]);

        $infologin = [
            'name' => $request->name,
            'password' => $request->password,
        ];
        if(Auth::attempt($infologin)){
            if (Auth::user()->role == 'superadmin'){
                return redirect('/dashboard-admin');
            }else{
                Auth::logout();
                return redirect('/panel-admin')->withErrors('Anda tidak memiliki akses ke halaman admin. Hanya Super Admin yang dapat login.')->withInput();
            }
        }else{
            return redirect('/panel-admin')->withErrors('Username Dan Password Yang Dimasukkan Tidak Sesuai')->withInput();
        }
    }
    public function logout_admin()
    {
        Auth::logout();
        return redirect('/panel-admin');
    }
}
