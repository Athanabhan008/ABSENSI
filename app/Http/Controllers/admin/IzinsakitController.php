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

class IzinsakitController extends Controller
{
    public function index()
    {
        $izinsakit = Vwizinsakit::all();
        return view('izinsakit.index', [
            'izinsakit' => $izinsakit,
            "active" => 'sakit'
        ]);
    }

    public function approve(Request $request, $id)
    {
        $izinsakit                                  = Sakit::find($id);
        $izinsakit->status_approve                  = $request->status_approve;
        $izinsakit->save();

        return back();
    }





}
