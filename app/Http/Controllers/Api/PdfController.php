<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
//    public function index()
//    {
//
//        $objectId = 1;
//
//        $object = Attention::with(['worker.person', 'vehicle', 'details', 'elements'])->find($objectId);
//
//        return view('orden-servicio', compact('object'))->render();
//    }

    public function getServiceOrder($id)
    {
        $object = Attention::getAttention($id);

//        HORIZONTAL
        $pdf = Pdf::loadView('orden-servicio', [
            'order' => $object
        ]);
//        $pdf->setPaper('a3', 'landscape');

//        return $object;
        return $pdf->stream('orden-servicio.pdf');
//        return $pdf->download('orden-servicio.pdf');
    }
}
