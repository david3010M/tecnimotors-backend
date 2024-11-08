<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportNoteResource extends JsonResource
{
    public function toArray($request)
    {
        $person = $this->sale->person;
        return [
            'correlativo' => $this->fullNumber,
            'fechaNota' => $this->date,
            'fechaVenta' => $this->sale->paymentDate,
            'documentoReferencial' => $this->sale->fullNumber,
            'cliente' => $person->typeofDocument === 'RUC' ? $person->businessName : $person->names . ' ' . $person->fatherSurname . ' ' . $person->motherSurname,
            'totalNota' => $this->totalCreditNote,
            'totalVenta' => $this->totalDocumentReference,
        ];
    }
}
