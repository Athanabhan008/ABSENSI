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
            'cuti' => $cuti
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
