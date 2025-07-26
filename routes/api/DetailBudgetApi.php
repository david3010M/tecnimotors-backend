<?php

use App\Http\Controllers\Api\DetailBudgetController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('detailbudget', [DetailBudgetController::class, 'list']);
    Route::get('detailbudget/{id}', [DetailBudgetController::class, 'show']);
    Route::post('detailbudget', [DetailBudgetController::class, 'store']);
    Route::put('detailbudget/{id}', [DetailBudgetController::class, 'update']);
    Route::delete('detailbudget/{id}', [DetailBudgetController::class, 'destroy']);
});