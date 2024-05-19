<?php

use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('user', UserController::class)->only(
    ['index', 'show', 'store', 'update', 'destroy']
)->names(
    [
        'index' => 'user.index',
        'store' => 'user.store',
        'show' => 'user.show',
        'update' => 'user.update',
        'destroy' => 'user.destroy',
    ]
);

Route::post('login', [AuthController::class, 'login']);
Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('authenticate', [AuthController::class, 'authenticate']);

    // SEARCH
    Route::get('searchByDni/{dni}', [SearchController::class, 'searchByDni']);
    Route::get('searchByRuc/{ruc}', [SearchController::class, 'searchByRuc']);
});
