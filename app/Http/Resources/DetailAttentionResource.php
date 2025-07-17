<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DetailAttentionResource extends JsonResource
{
    public function toArray($request)
    {
        $periodDetail = null;

        if ($this->dateRegister && $this->period) {
            $totalDays = (int) $this->period;
            $daysDiference = now()->diffInDays(Carbon::parse($this->dateRegister));
            $percentageDiference = round(($daysDiference / $totalDays) * 100);

            // Determinar las luces del semáforo
            if ($daysDiference > $totalDays) {
                $lights = ['rojo', 'rojo', 'rojo'];
                $statusColor = 'vencido';
            } elseif ($percentageDiference < 33) {
                $lights = ['verde', 'gris', 'gris'];
                $statusColor = 'primer_grupo';
            } elseif ($percentageDiference < 66) {
                $lights = ['verde', 'amarillo', 'gris'];
                $statusColor = 'segundo_grupo';
            } else {
                $lights = ['verde', 'amarillo', 'rojo'];
                $statusColor = 'tercer_grupo';
            }

            $periodDetail = [
                'days_diference' => $daysDiference,
                'percentage_diference' => $percentageDiference,
                'status_color' => $statusColor,
                'lights' => $lights,
            ];
        }

        $clientName = $this->attention?->vehicle?->person;
        $clientFullName = $clientName
            ?  trim("{$clientName->names} {$clientName->fatherSurname} {$clientName->motherSurname} {$clientName->businessName}")
            : null;

        return [
            'id' => $this->id,
            'saleprice' => $this->saleprice,
            'type' => $this->type,
            'date_max' => $this->dateMax,
            'comment' => $this->comment,
            'status' => $this->status,
            'percentage' => $this->percentage,
            'quantity' => $this->quantity,
            'date_register' => $this->dateRegister,
            'period' => $this->period,
            'period_detail' => $periodDetail,

            'service_name' => $this->service?->name,
            'worker_name' => $this->worker && $this->worker->person
                ? trim("{$this->worker->person->names} {$this->worker->person->fatherSurname} {$this->worker->person->motherSurname} {$this->worker->person->businessName}")
                : null,
                
            'product_name' => $this->product?->name,
            'attention_id' => $this->attention_id,
            'attention_code' => $this->attention?->correlativo,
            'plate' => $this->attention?->vehicle?->plate,
            'client_name' => $clientFullName,

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
