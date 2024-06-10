<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\budgetSheet;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{

    public function getServiceOrder($id)
    {
        $object = Attention::getAttention($id);

//        HORIZONTAL
        $pdf = Pdf::loadView('orden-servicio', [
            'order' => $object,
        ]);
//        $pdf->setPaper('a3', 'landscape');

//        return $object;
        return $pdf->stream('orden-servicio.pdf');
//        return $pdf->download('orden-servicio.pdf');
    }

    public function getBudgetSheet($id)
    {
        $object = budgetSheet::getBudgetSheet($id);

        $pdf = Pdf::loadView('presupuesto', [
            'budgetsheet' => $object,
        ]);

        return $pdf->stream('presupuesto' . $object->id . '.pdf');
//        return $pdf->download('orden-servicio.pdf');
    }
    public function getServiceOrder2($id)
    {
        $object = Attention::getAttention($id);
        $pdf = Pdf::loadView('orden-servicio2', [
            'attention' => $object,
        ]);
        return $pdf->stream('orden-servicio2.pdf');
    }
    public function getBudgetSheetInfo($id)
    {
        $object = budgetSheet::with(['attention.worker.person', 'attention.vehicle.person', 'attention.vehicle.brand',
            'attention.details', 'attention.routeImages', 'attention.elements'])->find($id);
        return $object;
    }
}
