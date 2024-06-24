<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlternativeController;

use App\Http\Controllers\CriteriaWeightController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NormalizationController;
use App\Http\Controllers\WeightedNormalization;
use App\Http\Controllers\RankController;
use App\Http\Controllers\Session;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\IdealSolution;
use App\Http\Controllers\IdealSolutionDistance;
use App\Http\Controllers\RankSAW;
use App\Http\Controllers\NormalizationSAW;
use App\Models\CriteriaWeight;

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


// Route::get('/login', [HomeController::class, 'login']);


Route::middleware(['guest'])->group(function () {
    Route::get('/', [Session::class, 'index'])->name('login');
    Route::post('/aksilogin', [Session::class, 'login']);


    
});




Route::resources([
'alternatives' => AlternativeController::class,

'criteriaweights' => CriteriaWeightController::class
]);


Route::get('/home', [Mahasiswa::class, 'profile'])->middleware('userAkses:mahasiswa');
Route::get('/formulir', [Mahasiswa::class, 'create'])->middleware('userAkses:mahasiswa');
Route::get('periode_beasiswa/{periode_id}/create', 'AlternativeController@create')->name('alternative.create');



Route::middleware(['auth'])->group(function(){

Route::get('/admin', [HomeController::class, 'dashboard'])->middleware('userAkses:admin');
Route::get('admin', [HomeController::class, 'dashboard'])->middleware('userAkses:admin');
Route::get('dashboard', [HomeController::class, 'dashboard'])->middleware('userAkses:admin');
Route::get('periode_beasiswa', [HomeController::class, 'periode'])->name('periode_beasiswa');;
Route::get('periode_beasiswa/tambah', [HomeController::class, 'view_periode'])->name('periode_beasiswa.tambah');
Route::post('periode_beasiswa/tambah/store', [HomeController::class, 'store_periode']);
Route::get('periode_beasiswa/view/{periode_file}', [HomeController::class, 'viewPdf'])->name('view_pdf');
Route::get('periode_beasiswa/{periode_id}/edit', [HomeController::class, 'periode_edit'])->name('periode.edit')->middleware('userAkses:admin,co_admin');
Route::put('periode_beasiswa/update', [HomeController::class, 'periode_update'])->name('periode.update')->middleware('userAkses:admin,co_admin');
Route::delete('periode_beasiswa/delete/{periode_id}', [HomeController::class, 'periode_destroy'])->name('periode.destroy')->middleware('userAkses:admin,co_admin');

Route::get('admin/alternative', [AlternativeController::class, 'view']);
Route::get('admin/{periode_id}/alternative',[AlternativeController::class, 'index'])->name('alternative.index');
Route::post('admin/{periode_id}/alternative/{alternative}/update', [AlternativeController::class, 'updateStatus'])->name('update.alternative.status');
Route::post('/alternativescores/{id}/update-status', [AlternativeController::class, 'updateStatus'])->name('alternativescores.updateStatus');
Route::patch('admin/alternative/{alternative}/update-multiple', [AlternativeController::class, 'updateMultiple'])->name('alternatives.updateMultiple');

////////////////////////////////////////// TOPSIS ////////////////////////////////////////////////
Route::get('admin/decision', [DecisionController::class, 'view']);
Route::get('admin/{periode_id}/decision',[DecisionController::class, 'index'])->name('decision.index');

Route::get('admin/normalization', [NormalizationController::class, 'view']);
Route::get('admin/{periode_id}/normalization',[NormalizationController::class, 'index'])->name('normalization.index');

Route::get('admin/weightednormalization', [WeightedNormalization::class, 'view']);
Route::get('admin/{periode_id}/weightednormalization',[WeightedNormalization::class, 'index'])->name('weightednormalization.index');

Route::get('admin/idealsolution', [IdealSolution::class, 'view']);
Route::get('admin/{periode_id}/idealsolution',[IdealSolution::class, 'index'])->name('idealsolution.index');

Route::get('admin/idealsolutiondistance', [IdealSolutionDistance::class, 'view']);
Route::get('admin/{periode_id}/idealsolutiondistance',[IdealSolutionDistance::class, 'index'])->name('idealsolutiondistance.index');

Route::get('admin/rank', [RankController::class, 'view']);
Route::get('admin/{periode_id}/rank',[RankController::class, 'index'])->name('rank.index');
Route::get('rank/pdf/{periode_id}', [RankController::class, 'generatePDF'])->name('rank.pdf');

//////////////////////////////////////////// SAW ////////////////////////////////////////////////////
Route::get('admin/normalizationSAW', [NormalizationSAW::class, 'view']);
Route::get('admin/{periode_id}/normalizationSAW',[NormalizationSAW::class, 'index'])->name('normalizationsaw.index');

Route::get('admin/rankSAW', [RankSAW::class, 'view']);
Route::get('admin/{periode_id}/rankSAW',[RankSAW::class, 'index'])->name('ranksaw.index');
Route::get('rankSAW/pdf/{periode_id}', [RankSAW::class, 'generatePDF'])->name('ranksaw.pdf');

Route::get('/co_admin', [HomeController::class, 'dashboard'])->middleware('userAkses:co_admin');
Route::get('co_admin', [HomeController::class, 'dashboard'])->middleware('userAkses:co_admin');
Route::get('/datamhs', [Mahasiswa::class, 'datamhs'])->middleware('userAkses:co_admin');

Route::get('periode_beasiswa/{periode_id}/edit', [HomeController::class, 'periode_edit'])->name('periode.edit')->middleware('userAkses:co_admin');
Route::put('periode_beasiswa/update', [HomeController::class, 'periode_update'])->name('periode.update')->middleware('userAkses:co_admin');
Route::delete('periode_beasiswa/delete/{periode_id}', [HomeController::class, 'periode_destroy'])->name('periode.destroy')->middleware('userAkses:co_admin');

Route::get('formulir', [Mahasiswa::class, 'formulir']);

Route::get('profile', [Mahasiswa::class, 'profile']);

Route::get('decision', [DecisionController::class, 'index'])->middleware('userAkses:admin');
Route::get('normalization', [NormalizationController::class, 'index'])->middleware('userAkses:admin');
Route::get('rank', [RankController::class, 'index'])->middleware('userAkses:admin');
Route::get('/logout', [Session::class, 'logout']);
});