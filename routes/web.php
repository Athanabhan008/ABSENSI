<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\admin\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\manager\ManagerController;
use App\Http\Controllers\CutiSakitController;
use App\Http\Controllers\admin\IzinsakitController;
use App\Http\Controllers\ProfileController;
use App\Models\Izinsakit;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/',[SesiController::class, 'index'])->name('login');
    Route::post('/',[SesiController::class, 'login']);
});

// Redirect /home ke login jika belum login, ke dashboard jika sudah login (hindari 404 saat kembali tanpa logout)
Route::get('/home', function () {
    return auth()->check() ? redirect('/dashboard') : redirect()->route('login');
})->name('home');

Route::middleware(['guest:web'])->group(function () {
    Route::get('/panel-admin', [SesiController::class, 'index_admin'])->name('login');
    Route::post('/panel-admin',[SesiController::class, 'login_admin']);
});


// Route::get('/error',[SesiController::class, 'error']);

Route::middleware(['UserAkses:superadmin,admin'])->group(function() {

    //DASHBOARD MANAGER
    Route::get('/dashboard',          [ManagerController::class, 'index'])->middleware('auth');
    Route::get('/dashboard-admin',    [DashboardController::class, 'index'])->middleware('UserAkses:superadmin');
    Route::get('/karyawan',           [KaryawanController::class, 'index']);
    Route::get('/izinsakit',          [IzinsakitController::class, 'index']);
    Route::get('/cuti',               [CutiController::class, 'index']);
    Route::get('/admin',              [ManagerController::class, 'index']);
    Route::get('/absen',              [AbsensiController::class, 'index']);
    Route::post('/absen/store',       [AbsensiController::class, 'store']);
    Route::post('/getabsensi',        [DashboardController::class, 'getabsensi']);
    Route::post('/showmap',           [DashboardController::class, 'showmap']);


    //PROFILE
    Route::get('/profile',                                [ProfileController::class, 'index']);
    Route::post('/profile/updateprofile/{id}',            [ProfileController::class, 'updateprofile']);
    Route::get('/absen/histori',                          [AbsensiController::class, 'histori'])->name('absen.histori');
    Route::get('/izin_sakit',                             [CutiSakitController::class, 'index']);
    Route::get('/izin_sakit/create',                      [CutiSakitController::class, 'create']);
    Route::post('/izin_sakit/store',                      [CutiSakitController::class, 'store']);
    Route::post('/izin_sakit/approve/{id}',               [IzinsakitController::class, 'approve']);
    Route::post('/cuti/approve/{id}',                     [CutiController::class, 'approve']);
});

//KARYAWAN
Route::get('/karyawan/create',                [KaryawanController::class, 'create']);
Route::post('/karyawan/store',                [KaryawanController::class, 'store']);
Route::get('/karyawan/edit/{id}',             [KaryawanController::class, 'edit']);
Route::post('/karyawan/update/{id}',          [KaryawanController::class, 'update']);
Route::get('/karyawan/edit-password/{id}',    [KaryawanController::class, 'edit_password']);
Route::post('/karyawan/update-password/{id}', [KaryawanController::class, 'update_password']);


Route::get('/logout',                        [SesiController::class, 'logout']);
Route::get('/logout_admin',                  [SesiController::class, 'logout_admin']);


