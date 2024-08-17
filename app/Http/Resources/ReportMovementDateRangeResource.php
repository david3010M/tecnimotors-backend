<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportMovementDateRangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $metodoPago = '';
        $metodoPago = (float)$this->yape > 0 ? implode(', ', ['Yape', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->plin > 0 ? implode(', ', ['Plin', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->deposit > 0 ? implode(', ', ['Depósito', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->cash > 0 ? implode(', ', ['Efectivo', $metodoPago]) : $metodoPago;
        $metodoPago = (float)$this->card > 0 ? implode(', ', ['Tarjeta', $metodoPago]) : $metodoPago;
        $metodoPago = substr($metodoPago, 0, -2);

        return [
            'numero' => $this->sequentialNumber,
            'fecha' => Carbon::parse($this->paymentDate)->format('d/m/Y'),
            'concepto' => $this->paymentConcept->name,
            'ingreso' => $this->paymentConcept->type === 'Ingreso' ? (float)$this->total : '',
            'egreso' => $this->paymentConcept->type === 'Egreso' ? (float)$this->total : '',
            'presupuesto' => $this->budgetSheet->number ?? '',
            'metodo_pago' => $metodoPago,
            'total' => (float)$this->total,
        ];
    }
}
