<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportAttendanceVehicleResource extends JsonResource
{
    public function toArray($request)
    {
        $client = $this->vehicle->person->typeofDocument === 'RUC' ? $this->vehicle->person->businessName :
            ($this->vehicle->person->names . ' ' . $this->vehicle->person->fatherSurname . ' ' . $this->vehicle->person->motherSurname);

//        OMITIR LOS NULL
        $services = $this->details->map(function ($detail) {
            if ($detail->type === 'Service') {
                return $detail->service;
            } else {
                return null;
            }
        })->filter();

        $serviceString = $services->map(function ($service) {
            return $service->name;
        })->implode(', ');


        $responsable = $this->worker->person->names . ' ' . $this->worker->person->fatherSurname . ' ' . $this->worker->person->motherSurname;

        return [
            'fecha' => Carbon::parse($this->arrivalDate)->format('d/m/Y'),
            'numero' => $this->number,
            'cliente' => $client,
            'marca' => $this->vehicle->vehicleModel->brand->name,
            'modelo' => $this->vehicle->vehicleModel->name,
            'placa' => $this->vehicle->plate,
            'kilometraje' => (float)$this->km,
            'anio' => $this->vehicle->year,
            'servicio' => $serviceString,
            'responsable' => $responsable,
            'recepcion' => $this->driver,
            'metodo' => $this->budgetSheet->paymentType,
            'pago' => (float)$this->budgetSheet->total,
            'debe' => $this->budgetSheet->debtAmount > 0 ? 'SI' : 'NO',
        ];
    }
}
