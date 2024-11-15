<?php

namespace App\Http\Requests;

use App\Models\Guide;

/**
 * @OA\Schema(
 *     title="StoreGuideRequest",
 *     type="object",
 *     @OA\Property(property="date_emision", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="date_traslado", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="modality", type="string", example="TRANSPORTE PRIVADO"),
 *     @OA\Property(property="recipient_id", type="integer", example="1"),
 *     @OA\Property(property="worker_id", type="integer", example="1"),
 *     @OA\Property(property="driver_licencia", type="string", example="12345678"),
 *     @OA\Property(property="vehicle_placa", type="string", example="12345678"),
 *     @OA\Property(property="nro_paquetes", type="integer", example="1"),
 *     @OA\Property(property="transbordo", type="boolean", example=false),
 *     @OA\Property(property="district_id_start", type="string", example="01"),
 *     @OA\Property(property="address_start", type="string", example="Av. Peru"),
 *     @OA\Property(property="district_id_end", type="string", example="01"),
 *     @OA\Property(property="address_end", type="string", example="Av. Peru"),
 *     @OA\Property(property="observation", type="string", example="Observacion"),
 *     @OA\Property(property="factura", type="string", example="F-0001"),
 *     @OA\Property(property="guide_motive_id", type="integer", example="1"),
 *     @OA\Property(property="details", type="array", @OA\Items(
 *         @OA\Property(property="code", type="string", example="001"),
 *         @OA\Property(property="description", type="string", example="Producto 1"),
 *         @OA\Property(property="unit", type="string", example="NIU"),
 *         @OA\Property(property="quantity", type="integer", example="1"),
 *         @OA\Property(property="weight", type="number", example="1.0"),
 *     )),
 * )
 */
class StoreGuideRequest extends StoreRequest
{
    public function rules()
    {
        return [
            'date_emision' => 'required|date',
            'date_traslado' => 'required|date',
            'modality' => 'required|string|in:' . implode(',', Guide::modalities),

//            CONDUCTOR
            'driver_licencia' => 'required|string',
            'vehicle_placa' => 'required|string',

//            GUIA
            'nro_paquetes' => 'nullable|integer',
            'transbordo' => 'nullable|boolean',
            'district_id_start' => 'nullable|string|exists:districts,id',
            'address_start' => 'required|string',
            'district_id_end' => 'nullable|string|exists:districts,id',
            'address_end' => 'required|string',
            'observation' => 'nullable|string',
            'factura' => 'nullable|string',
            'guide_motive_id' => 'nullable|exists:guide_motives,id',
            'recipient_id' => 'required|exists:people,id',
            'worker_id' => 'required|exists:people,id',

//            DETALLES
            'details' => 'required|array|min:1',
            'details.*.code' => 'required|string',
            'details.*.description' => 'required|string',
            'details.*.unit' => 'required|string',
            'details.*.quantity' => 'required|integer',
            'details.*.weight' => 'required|numeric',
        ];
    }
}
