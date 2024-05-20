<?php

use App\Http\Controllers\Api\GroupMenuController;
use App\Http\Controllers\Api\OptionMenuController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TypeUserController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkerController;
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

Route::post('login', [AuthController::class, 'login']);

Route::group(["middleware" => ["auth:sanctum"]], function () {

    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('authenticate', [AuthController::class, 'authenticate']);

    // SEARCH
    Route::get('searchByDni/{dni}', [SearchController::class, 'searchByDni']);
    Route::get('searchByRuc/{ruc}', [SearchController::class, 'searchByRuc']);

    //USER
    Route::resource('user', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'user.index', 'store' => 'user.store', 'show' => 'user.show', 'update' => 'user.update', 'destroy' => 'user.destroy']);

    //GROUP MENU
    Route::resource('groupmenu', GroupMenuController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'groupmenu.index', 'store' => 'groupmenu.store', 'show' => 'groupmenu.show', 'update' => 'groupmenu.update', 'destroy' => 'groupmenu.destroy']);

    //PERSON
    Route::resource('person', PersonController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'person.index', 'store' => 'person.store', 'show' => 'person.show', 'update' => 'person.update', 'destroy' => 'person.destroy']);

    //OPTION MENU
    Route::resource('optionMenu', OptionMenuController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'optionMenu.index', 'store' => 'optionMenu.store', 'show' => 'optionMenu.show', 'update' => 'optionMenu.update', 'destroy' => 'optionMenu.destroy']);

    //WORKER
    Route::resource('worker', WorkerController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'worker.index', 'store' => 'worker.store', 'show' => 'worker.show', 'update' => 'worker.update', 'destroy' => 'worker.destroy']);

    //TYPEUSER
    Route::resource('typeUser', TypeUserController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'typeUser.index', 'store' => 'typeUser.store', 'show' => 'typeUser.show',
            'update' => 'typeUser.update', 'destroy' => 'typeUser.destroy']);

    Route::post('typeUser/setAccess', [TypeUserController::class, 'setAccess'])->name('typeUser.setAccess');

});
