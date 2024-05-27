<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;

class PdfController extends Controller
{
    public function index()
    {
   
        $objectId = 1;

        $object = Attention::with(['worker.person', 'vehicle', 'details', 'elements'])->find($objectId);

        return view('orden-servicio', compact('object'))->render();
    }
}
