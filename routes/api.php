<?php

use App\Http\Controllers\Api\SbwController;
use App\Http\Controllers\ApiWipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ApiWipController::class)
    ->prefix('apibk')
    ->name('apibk.')
    ->group(function () {
        Route::get('/', 'bk_pilih')->name('index');
        Route::get('/get_sum_wip', 'get_sum_wip')->name('get_sum_wip');
        Route::get('/bkCetakApi', 'bkCetakApi')->name('bkCetakApi');
        Route::get('/sumWip', 'sumWip')->name('sumWip');
        Route::get('/detailSumWip', 'detailSumWip')->name('detailSumWip');
        Route::get('/bkCbtAwal', 'bkCbtAwal')->name('bkCbtAwal');
        Route::get('/sumCtk', 'sumCtk')->name('sumCtk');
        Route::get('/detailOpname/{no}', 'detailOpname')->name('detailOpname');
        Route::get('/bkSortirApi', 'bkSortirApi')->name('bkSortirApi');
        Route::get('/partai', 'partai')->name('partai');
        Route::get('/sum_partai', 'sum_partai')->name('sum_partai');
    });
Route::controller(SbwController::class)
    ->prefix('sbw')
    ->name('sbw.')
    ->group(function () {
        Route::get('/sbw_kotor', 'sbw_kotor')->name('sbw_kotor');
        Route::get('/getPartai', 'getPartai')->name('getPartai');
    });
