<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportSaleResource extends JsonResource
{
    public function toArray($request)
    {
        $metodoPago = '';
        $metodoPago = (float)$this->yape > 0 ? implode(', ', ['Yape', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->plin > 0 ? implode(', ', ['Plin', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->deposit > 0 ? implode(', ', ['Depósito', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->effective > 0 ? implode(', ', ['Efectivo', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->card > 0 ? implode(', ', ['Tarjeta', $metodoPago]) : $metodoPago;
        $metodoPago = substr($metodoPago, 0, -2);

        $person = $this->person;

        return [
            'correlativo' => $this->fullNumber,
            'fecha' => Carbon::parse($this->paymentDate)->format('d/m/Y'),
            'tipoDocumento' => $this->documentType,
            'tipoPago' => $this->paymentType,
            'cliente' => $this->person->typeofDocument === 'RUC' ? $person->businessName : $person->names . ' ' . $person->fatherSurname . ' ' . $person->motherSurname,
            'estado' => $this->status,
//            'metodoPago' => $metodoPago,
            'total' => (float)$this->total,
        ];
    }
}
