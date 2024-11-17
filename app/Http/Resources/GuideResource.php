<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="GuideResource",
 *     title="GuideResource",
 *     description="Guide resource",
 *     @OA\Property (property="id", type="integer", example="1"),
 *     @OA\Property (property="number", type="string", example="G-0001"),
 *     @OA\Property (property="full_number", type="string", example="G-0001"),
 *     @OA\Property (property="date_emision", type="string", format="date", example="2024-08-19"),
 *     @OA\Property (property="date_traslado", type="string", format="date", example="2024-08-19"),
 *     @OA\Property (property="motive_name", type="string", example="Venta"),
 *     @OA\Property (property="modality", type="string", example="Venta"),
 *     @OA\Property (property="recipient_names", type="string", example="Juan Perez"),
 *     @OA\Property (property="recipient_document", type="string", example="12345678"),
 *     @OA\Property (property="driver_names", type="string", example="Juan Perez"),
 *     @OA\Property (property="driver_surnames", type="string", example="Perez"),
 *     @OA\Property (property="driver_document", type="string", example="12345678"),
 *     @OA\Property (property="vehicle_placa", type="string", example="12345678"),
 *     @OA\Property (property="driver_licencia", type="string", example="12345678"),
 *     @OA\Property (property="nro_paquetes", type="integer", example="1"),
 *     @OA\Property (property="transbordo", type="string", example="No"),
 *     @OA\Property (property="cod_motive", type="string", example="01"),
 *     @OA\Property (property="net_weight", type="number", example="1.0"),
 *     @OA\Property (property="ubigeo_end", type="string", example="01"),
 *     @OA\Property (property="address_end", type="string", example="Av. Peru"),
 *     @OA\Property (property="ubigeo_start", type="string", example="01"),
 *     @OA\Property (property="address_start", type="string", example="Av. Peru"),
 *     @OA\Property (property="observation", type="string", example="Observacion"),
 *     @OA\Property (property="factura", type="string", example="F-0001"),
 *     @OA\Property (property="status_facturado", type="boolean", example="false"),
 *     @OA\Property (property="user_id", type="integer", example="1"),
 *     @OA\Property (property="branch_id", type="integer", example="1"),
 *     @OA\Property (property="created_at", type="string", format="date-time", example="2024-08-19T00:00:00.000000Z"),
 *     @OA\Property (property="details", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example="1"),
 *         @OA\Property(property="code", type="string", example="001"),
 *         @OA\Property(property="description", type="string", example="Producto 1"),
 *         @OA\Property(property="unit", type="string", example="UND"),
 *         @OA\Property(property="quantity", type="integer", example="1"),
 *         @OA\Property(property="weight", type="number", example="1.0"),
 *         @OA\Property(property="guide_id", type="integer", example="1"),
 *     )),
 * )
 *
 *
 * @OA\Schema (
 *     schema="GuideCollection",
 *     title="GuideCollection",
 *     description="Guide resource collection",
 *     @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/GuideResource")),
 *     @OA\Property (property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property (property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 */
class GuideResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'full_number' => $this->full_number,
            'date_emision' => $this->date_emision,
            'date_traslado' => $this->date_traslado,
            'motive_name' => $this->motive_name,
            'modality' => $this->modality,
            'recipient_names' => $this->recipient_names,
            'recipient_document' => $this->recipient_document,
            'driver_fullnames' => $this->driver_fullnames,
            'driver_names' => $this->driver_names,
            'driver_surnames' => $this->driver_surnames,
            'driver_document' => $this->driver_document,
            'vehicle_placa' => $this->vehicle_placa,
            'driver_licencia' => $this->driver_licencia,
            'nro_paquetes' => $this->nro_paquetes,
            'transbordo' => $this->transbordo,
            'cod_motive' => $this->cod_motive,
            'net_weight' => $this->net_weight,
            'ubigeo_end' => $this->ubigeo_end,
            'address_end' => $this->address_end,
            'ubigeo_start' => $this->ubigeo_start,
            'address_start' => $this->address_start,
            'observation' => $this->observation,
            'factura' => $this->factura,
            'status_facturado' => $this->status_facturado,
            'user_id' => $this->user_id,
            'branch_id' => $this->branch_id,
            'guide_motive_id' => $this->guide_motive_id,
            'created_at' => $this->created_at,
            'details' => $this->details,
            'districtStart' => $this->districtStart,
            'districtEnd' => $this->districtEnd,
        ];

    }
}
