<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DetailBudgetResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="saleprice", type="number", format="float"),
 *     @OA\Property(property="quantity", type="integer"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="comment", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", nullable=true),
 *     @OA\Property(property="dateRegister", type="string", format="date", nullable=true),
 *     @OA\Property(property="dateMax", type="string", format="date", nullable=true),
 *     @OA\Property(property="dateCurrent", type="string", format="date", nullable=true),
 *     @OA\Property(property="percentage", type="integer"),
 *     @OA\Property(property="period", type="integer"),
 *     @OA\Property(property="budget_sheet_id", type="integer"),
 *     @OA\Property(property="budget_sheet_number", type="string", nullable=true),
 *     @OA\Property(property="worker_id", type="integer"),
 *     @OA\Property(property="worker_name", type="string", nullable=true),
 *     @OA\Property(property="service_id", type="integer"),
 *     @OA\Property(property="service_name", type="string", nullable=true),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="product_name", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true)
 * )
 */

class DetailBudgetResource extends JsonResource
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

        $clientName = $this?->budget_sheet?->attention?->vehicle?->person;
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
            'worker_id' => $this->worker_id,
            'worker_name' => $this->worker && $this->worker->person
                ? trim("{$this->worker->person->names} {$this->worker->person->fatherSurname} {$this->worker->person->motherSurname} {$this->worker->person->businessName}")
                : null,
                
            'product_name' => $this->product?->name,
            'budget_sheet_id' => $this->budget_sheet_id,
            'budget_sheet_number' => $this->budget_sheet?->number,
            'plate' => $this->budget_sheet?->attention?->vehicle?->plate,
            'client_name' => $clientFullName,

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}