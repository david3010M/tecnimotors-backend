<?php

use App\Http\Controllers\Api\AttentionController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\BudgetSheetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommitmentController;
use App\Http\Controllers\Api\ConceptMovController;
use App\Http\Controllers\Api\ConceptPayController;
use App\Http\Controllers\Api\DetailAttentionController;
use App\Http\Controllers\Api\ElementController;
use App\Http\Controllers\Api\ElementForAttentionController;
use App\Http\Controllers\Api\GroupMenuController;
use App\Http\Controllers\Api\MovimentController;
use App\Http\Controllers\Api\OptionMenuController;
use App\Http\Controllers\Api\PdfController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SendWhatsappController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\SpecialtyPersonController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TypeAttentionController;
use App\Http\Controllers\Api\TypeUserController;
use App\Http\Controllers\Api\TypeVehicleController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
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

Route::post('login', [AuthController::class, 'login'])->name('login');

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

    // TYPE ATTENTION
    Route::resource('typeAttention', TypeAttentionController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'typeAttention.index', 'store' => 'typeAttention.store', 'show' => 'typeAttention.show',
            'update' => 'typeAttention.update', 'destroy' => 'typeAttention.destroy']);

    // TYPE VEHICLE
    Route::resource('typeVehicle', TypeVehicleController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'typeVehicle.index', 'store' => 'typeVehicle.store', 'show' => 'typeVehicle.show',
            'update' => 'typeVehicle.update', 'destroy' => 'typeVehicle.destroy']);

    //  BRAND
    Route::resource('brand', BrandController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'brand.index', 'store' => 'brand.store', 'show' => 'brand.show', 'update' => 'brand.update', 'destroy' => 'brand.destroy']);

    //  ELEMENT
    Route::resource('element', ElementController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'element.index', 'store' => 'element.store', 'show' => 'element.show', 'update' => 'element.update', 'destroy' => 'element.destroy']);

    //  VEHICLE
    Route::resource('vehicle', VehicleController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'vehicle.index', 'store' => 'vehicle.store', 'show' => 'vehicle.show', 'update' => 'vehicle.update', 'destroy' => 'vehicle.destroy']);

    Route::get('vehicleByPerson/{id}', [VehicleController::class, 'getVehiclesByPerson']);

//  ATTENTION
    Route::resource('attention', AttentionController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'attention.index', 'store' => 'attention.store',
            'show' => 'attention.show', 'update' => 'attention.update',
            'destroy' => 'attention.destroy']);
    Route::get('searchByNumber/{number}', [AttentionController::class, 'searchByNumber']);

// ELEMENTFORATTENTION
    Route::resource('elementForAttention', ElementForAttentionController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'elementForAttention.index', 'store' => 'elementForAttention.store', 'show' => 'elementForAttention.show', 'update' => 'elementForAttention.update', 'destroy' => 'elementForAttention.destroy']);

    // ELEMENT
    Route::resource('element', ElementController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'element.index', 'store' => 'element.store', 'show' => 'element.show', 'update' => 'element.update', 'destroy' => 'element.destroy']);
//    SERVICE
    Route::resource('service', ServiceController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'service.index', 'store' => 'service.store', 'show' => 'service.show', 'update' => 'service.update', 'destroy' => 'service.destroy']);

//    UNIT
    Route::resource('unit', UnitController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'unit.index', 'store' => 'unit.store', 'show' => 'unit.show', 'update' => 'unit.update', 'destroy' => 'unit.destroy']);

//    CATEGORY
    Route::resource('category', CategoryController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'category.index', 'store' => 'category.store', 'show' => 'category.show', 'update' => 'category.update', 'destroy' => 'category.destroy']);

//    PRODUCT
    Route::resource('product', ProductController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'product.index', 'store' => 'product.store', 'show' => 'product.show', 'update' => 'product.update', 'destroy' => 'product.destroy']);

//    SUPPLIER
    Route::resource('supplier', SupplierController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'supplier.index', 'store' => 'supplier.store', 'show' => 'supplier.show', 'update' => 'supplier.update', 'destroy' => 'supplier.destroy']);

//    CONCEPT MOV
    Route::resource('conceptMov', ConceptMovController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'conceptMov.index', 'store' => 'conceptMov.store', 'show' => 'conceptMov.show', 'update' => 'conceptMov.update', 'destroy' => 'conceptMov.destroy']);

//    BANK
    Route::resource('bank', BankController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'bank.index', 'store' => 'bank.store', 'show' => 'bank.show', 'update' => 'bank.update', 'destroy' => 'bank.destroy']);

//    CONCEPT PAY
    Route::resource('conceptPay', ConceptPayController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'conceptPay.index', 'store' => 'conceptPay.store', 'show' => 'conceptPay.show', 'update' => 'conceptPay.update', 'destroy' => 'conceptPay.destroy']);

//    SPECIALTY
    Route::resource('specialty', SpecialtyController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'specialty.index', 'store' => 'specialty.store', 'show' => 'specialty.show', 'update' => 'specialty.update', 'destroy' => 'specialty.destroy']);
//    SPECIALTY
    Route::resource('specialtyPerson', SpecialtyPersonController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'specialtyPerson.index', 'store' => 'specialtyPerson.store', 'show' => 'specialtyPerson.show', 'update' => 'specialtyPerson.update', 'destroy' => 'specialtyPerson.destroy']);

//    ORDER SERVICE
//    Route::get('getServiceOrder', [PdfController::class, 'index']);

    Route::post('sendSheetByWhatsapp', [SendWhatsappController::class, 'sendSheetServiceByWhatsapp']);
    Route::post('sendBudgetSheetByWhatsapp', [SendWhatsappController::class, 'sendBudgetSheetByWhatsapp']);

    Route::get('detailAttentionByWorker/{id}', [DetailAttentionController::class, 'detailAttentionByWorker'])
        ->name('detailAttention.getDetailAttentionByWorkerId');
    Route::post('detailAttentionStart/{id}', [DetailAttentionController::class, 'start'])
        ->name('detailAttention.start');
    Route::post('detailAttentionFinish/{id}', [DetailAttentionController::class, 'finish'])
        ->name('detailAttention.finish');
    Route::get('detailAttention/{id}', [DetailAttentionController::class, 'show'])->name('detailAttention.show');
    Route::put('detailAttention/{id}', [DetailAttentionController::class, 'update'])->name('detailAttention.update');

    Route::resource('budgetSheet', BudgetSheetController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'budgetSheet.index', 'store' => 'budgetSheet.store', 'show' => 'budgetSheet.show',
            'update' => 'budgetSheet.update', 'destroy' => 'budgetSheet.destroy']);

    Route::get('taskByDetailAttention/{id}', [TaskController::class, 'getTaskByDetailAttention']);
    Route::post('taskEvidence/{id}', [TaskController::class, 'storeEvidence']);
    Route::resource('task', TaskController::class)->only(['show', 'store', 'update', 'destroy'])
        ->names(['store' => 'task.store', 'show' => 'task.show', 'update' => 'task.update', 'destroy' => 'task.destroy']);

    // MOVEMENT
    Route::get('moviment', [MovimentController::class, 'index']);
    Route::get('moviment/{id}', [MovimentController::class, 'show']);
    Route::post('moviment', [MovimentController::class, 'store']);
    Route::post('movimentAperturaCierre', [MovimentController::class, 'aperturaCierre']);
    Route::delete('moviment/{id}', [MovimentController::class, 'destroy']);
    Route::put('moviment/{id}', [MovimentController::class, 'update']);
    Route::get('reportCaja', [PdfController::class, 'reportCaja'])->name('reportCaja');

    Route::get('typeUser/{id}/access', [TypeUserController::class, 'getAccess']);

//    COMMITMENT
    Route::resource('commitment', CommitmentController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'commitment.index', 'store' => 'commitment.store', 'show' => 'commitment.show',
            'update' => 'commitment.update', 'destroy' => 'commitment.destroy']);
});
