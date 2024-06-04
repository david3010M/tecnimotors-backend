<?php

use App\Http\Controllers\Api\BudgetSheetController;
use App\Http\Controllers\Api\PdfController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

//Route::get('ordenservicio', [PdfController::class, 'index']);
Route::get('ordenservicio/{id}', [PdfController::class, 'getServiceOrder'])->name('ordenservicio');
Route::get('presupuesto/{id}', [PdfController::class, 'getBudgetSheet'])->name('presupuesto');
Route::get('presupuestoInfo/{id}', [PdfController::class, 'getBudgetSheetInfo'])->name('presupuesto.info');
