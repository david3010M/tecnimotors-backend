<?php

use App\Http\Controllers\Api\AmortizationController;
use App\Http\Controllers\Api\AttentionController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\BudgetSheetController;
use App\Http\Controllers\Api\CashController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommitmentController;
use App\Http\Controllers\Api\ConceptMovController;
use App\Http\Controllers\Api\ConceptPayController;
use App\Http\Controllers\Api\DetailAttentionController;
use App\Http\Controllers\Api\ElementController;
use App\Http\Controllers\Api\ElementForAttentionController;
use App\Http\Controllers\Api\ExtensionController;
use App\Http\Controllers\Api\GroupMenuController;
use App\Http\Controllers\Api\MovimentController;
use App\Http\Controllers\Api\OcupationController;
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
use App\Http\Controllers\Api\UbigeoController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\VehicleModelController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ConcessionController;
use App\Http\Controllers\ExcelReportController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\GuideMotiveController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NoteReasonController;
use App\Http\Controllers\SaleController;
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

//PENDIENTE DE PONERLO EN EL AUTH

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(["middleware" => ["auth:sanctum"]], function () {

    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('authenticate', [AuthController::class, 'authenticate']);
    Route::get('/logs', [AuthController::class, 'logs'])->name('logs');

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

    // CONCESSION
    Route::resource('concession', ConcessionController::class)->only(['index', 'show', 'store', 'destroy'])
        ->names(['index' => 'concession.index', 'store' => 'concession.store', 'show' => 'concession.show', 'destroy' => 'concession.destroy']);
    Route::post('concession/{id}', [ConcessionController::class, 'update'])->name('concession.update');

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
            'show' => 'attention.show',
            'destroy' => 'attention.destroy']);
    Route::get('searchByNumber/{number}', [AttentionController::class, 'searchByNumber']);
    Route::post('attention/{id}', [AttentionController::class, 'update']);
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

//    OCUPATION
    Route::resource('ocupation', OcupationController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'ocupation.index', 'store' => 'ocupation.store', 'show' => 'ocupation.show', 'update' => 'ocupation.update', 'destroy' => 'ocupation.destroy']);

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
    Route::resource('specialty', SpecialtyController::class)->only(['index', 'show', 'store',
        'update', 'destroy'])
        ->names(['index' => 'specialty.index', 'store' => 'specialty.store',
            'show' => 'specialty.show', 'update' => 'specialty.update',
            'destroy' => 'specialty.destroy']);
//    SPECIALTY
    Route::resource('specialtyPerson', SpecialtyPersonController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'specialtyPerson.index', 'store' => 'specialtyPerson.store', 'show' => 'specialtyPerson.show', 'update' => 'specialtyPerson.update', 'destroy' => 'specialtyPerson.destroy']);

//    ORDER SERVICE
//    Route::get('getServiceOrder', [PdfController::class, 'index']);

    Route::post('sendSheetByWhatsapp', [SendWhatsappController::class, 'sendSheetServiceByWhatsapp']);
    Route::post('sendBudgetSheetByWhatsapp', [SendWhatsappController::class, 'sendBudgetSheetByWhatsapp']);
    Route::post('sendEvidenceByWhatsapp', [SendWhatsappController::class, 'sendEvidenceByWhatsapp']);

    Route::get('detailAttentionByWorker/{id}', [DetailAttentionController::class, 'detailAttentionByWorker'])
        ->name('detailAttention.getDetailAttentionByWorkerId');
    Route::post('detailAttentionStart/{id}', [DetailAttentionController::class, 'start'])
        ->name('detailAttention.start');
    Route::post('detailAttentionFinish/{id}', [DetailAttentionController::class, 'finish'])
        ->name('detailAttention.finish');
    Route::get('detailAttention/{id}', [DetailAttentionController::class, 'show'])->name('detailAttention.show');
    Route::put('detailAttention/{id}', [DetailAttentionController::class, 'update'])->name('detailAttention.update');

    Route::get('budgetSheet/findBudgetSheetByPersonId', [BudgetSheetController::class, 'findBudgetSheetByPersonId']);
    Route::resource('budgetSheet', BudgetSheetController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'budgetSheet.index',
            'store' => 'budgetSheet.store',
            'show' => 'budgetSheet.show',
            'update' => 'budgetSheet.update',
            'destroy' => 'budgetSheet.destroy',
        ]);

    Route::put('budgetSheet/{id}/updateStatusSinBoletear', [BudgetSheetController::class, 'updateStatusSinBoletear']);

    Route::get('taskByDetailAttention/{id}', [TaskController::class, 'getTaskByDetailAttention']);
    Route::post('taskEvidence/{id}', [TaskController::class, 'storeEvidence']);
    Route::delete('taskEvidence/{id}', [TaskController::class, 'deleteEvidence']);
    Route::get('taskEvidence/{id}', [TaskController::class, 'listEvidence']);
    Route::get('taskEvidenceByAttention/{id}', [TaskController::class, 'listEvidenceByAttention']);

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
    Route::get('movimentLast', [MovimentController::class, 'showLastMovPayment']);
    Route::get('getArchivosDocument/{id}/{tipodocumento}', [SaleController::class, 'getArchivosDocument']);
    Route::get('sendemail/{id}', [SaleController::class, 'sendemail']);


    Route::get('typeUser/{id}/access', [TypeUserController::class, 'getAccess']);

    Route::post('storeByOccupation', [WorkerController::class, 'storeByOccupation']);
    Route::put('updateByOccupation/{id}', [WorkerController::class, 'updateByOccupation']);

    Route::get('getCorrelative', [AttentionController::class, 'getCorrelativo']);

//    VEHICLE MODEL
    Route::resource('vehicleModel', VehicleModelController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'vehicleModel.index', 'store' => 'vehicleModel.store', 'show' => 'vehicleModel.show',
            'update' => 'vehicleModel.update', 'destroy' => 'vehicleModel.destroy']);

//    COMMITMENT
    Route::resource('commitment', CommitmentController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'commitment.index', 'store' => 'commitment.store', 'show' => 'commitment.show',
            'update' => 'commitment.update', 'destroy' => 'commitment.destroy']);

//    AMORTIZATION INDEX
    Route::post('amortization', [AmortizationController::class, 'store'])->name('amortization.store');
    Route::get('amortizationsByCommitmentId/{id}', [AmortizationController::class, 'amortizationsByCommitmentId']);

//REPORTES
    Route::get('reportMovementClient/{id}', [ExcelReportController::class, 'reportMovementClient'])->name('reportMovementClient');
    Route::get('reportAttendanceVehicle', [ExcelReportController::class, 'reportAttendanceVehicle'])->name('reportAttendanceVehicle');
    Route::get('reportMovementVehicle', [ExcelReportController::class, 'reportMovementVehicle'])->name('reportMovementVehicle');
    Route::get('reportServicios', [ExcelReportController::class, 'reportService'])->name('reportService');
    Route::get('reportMovementDateRange/{id}', [ExcelReportController::class, 'reportMovementDateRange'])->name('reportMovementDateRange');
    Route::get('reportCommitment', [ExcelReportController::class, 'reportCommitment'])->name('reportCommitment');
    Route::get('reportSaleProducts', [ExcelReportController::class, 'reportSaleProducts'])->name('reportSaleProducts');
    Route::get('reportSales', [ExcelReportController::class, 'reportSale'])->name('reportSale');
    Route::get('reportNotes', [ExcelReportController::class, 'reportNote'])->name('reportNote');

    Route::get('showAperturaMovements', [MovimentController::class, 'showAperturaMovements']);

    Route::get('person/{id}/vehicles', [PersonController::class, 'vehiclesByPerson']);
    Route::get('person/{id}/attentions', [PersonController::class, 'attentionsByPerson']);

    Route::get('saleProducts', [ProductController::class, 'saleProducts']);

//    EXTENSION
    Route::resource('extension', ExtensionController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'extension.index', 'store' => 'extension.store', 'show' => 'extension.show',
            'update' => 'extension.update', 'destroy' => 'extension.destroy']);

//    BRANCH
    Route::resource('branch', BranchController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'branch.index', 'store' => 'branch.store', 'show' => 'branch.show',
            'update' => 'branch.update', 'destroy' => 'branch.destroy']);

//    CASH
    Route::resource('cash', CashController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'cash.index', 'store' => 'cash.store', 'show' => 'cash.show',
            'update' => 'cash.update', 'destroy' => 'cash.destroy']);

//    SALE
    Route::resource('sale', SaleController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'sale.index', 'store' => 'sale.store', 'show' => 'sale.show',
            'update' => 'sale.update', 'destroy' => 'sale.destroy']);

    //    NOTE REASON
    Route::resource('noteReason', NoteReasonController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'noteReason.index', 'store' => 'noteReason.store', 'show' => 'noteReason.show',
            'update' => 'noteReason.update', 'destroy' => 'noteReason.destroy']);

//    NOTE
    Route::resource('note', NoteController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'note.index', 'store' => 'note.store', 'show' => 'note.show',
            'update' => 'note.update', 'destroy' => 'note.destroy']);

//    GUIDE
    Route::resource('guide', GuideController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'guide.index', 'store' => 'guide.store', 'show' => 'guide.show',
            'update' => 'guide.update', 'destroy' => 'guide.destroy']);

//    UBIGEO
    Route::get('departments', [UbigeoController::class, 'indexDepartments'])->name('indexDepartments');
    Route::get('provinces/{departmentId}', [UbigeoController::class, 'indexProvinces'])->name('indexProvinces');
    Route::get('districts/{provinceId}', [UbigeoController::class, 'indexDistricts'])->name('indexDistricts');
    Route::get('ubigeos', [UbigeoController::class, 'ubigeos'])->name('ubigeos');

//    GUIDE MOTIVE
    Route::resource('guide-motives', GuideMotiveController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names(['index' => 'guide-motives.index', 'store' => 'guide-motives.store', 'show' => 'guide-motives.show',
            'update' => 'guide-motives.update', 'destroy' => 'guide-motives.destroy']);
});
