<?php

namespace App\Http\Requests;

use App\Models\Guide;

/**
 * @OA\Schema(
 *     title="StoreGuideRequest",
 *     type="object",
 *     @OA\Property(property="number", type="string", example="G-0001"),
 *     @OA\Property(property="full_number", type="string", example="G-0001"),
 *     @OA\Property(property="date_emision", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="date_traslado", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="motive_name", type="string", example="VENTA"),
 *     @OA\Property(property="modality", type="string", example="TRANSPORTE PRIVADO"),
 *     @OA\Property(property="recipient_names", type="string", example="Juan Perez"),
 *     @OA\Property(property="recipient_document", type="string", example="12345678"),
 *     @OA\Property(property="driver_names", type="string", example="Juan Perez"),
 *     @OA\Property(property="driver_surnames", type="string", example="Perez"),
 *     @OA\Property(property="driver_document", type="string", example="12345678"),
 *     @OA\Property(property="vehicle_placa", type="string", example="12345678"),
 *     @OA\Property(property="driver_licencia", type="string", example="12345678"),
 *     @OA\Property(property="nro_paquetes", type="integer", example="1"),
 *     @OA\Property(property="transbordo", type="boolean", example=false),
 *     @OA\Property(property="cod_motive", type="string", example="01"),
 *     @OA\Property(property="net_weight", type="number", example="1.0"),
 *     @OA\Property(property="ubigeo_end", type="string", example="01"),
 *     @OA\Property(property="address_end", type="string", example="Av. Peru"),
 *     @OA\Property(property="ubigeo_start", type="string", example="01"),
 *     @OA\Property(property="address_start", type="string", example="Av. Peru"),
 *     @OA\Property(property="observation", type="string", example="Observacion"),
 *     @OA\Property(property="factura", type="string", example="F-0001"),
 *     @OA\Property(property="status_facturado", type="boolean", example="false"),
 *     @OA\Property(property="details", type="array", @OA\Items(
 *         @OA\Property(property="code", type="string", example="001"),
 *         @OA\Property(property="description", type="string", example="Producto 1"),
 *         @OA\Property(property="unit", type="string", example="UND"),
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
            'number' => 'nullable|string',
            'full_number' => 'nullable|string',
            'date_emision' => 'nullable|date',
            'date_traslado' => 'nullable|date',
            'motive_name' => 'nullable|string|in:' . implode(',', Guide::motives),
            'modality' => 'nullable|string|in:' . implode(',', Guide::modalities),
            'recipient_names' => 'nullable|string',
            'recipient_document' => 'nullable|string',
            'driver_names' => 'nullable|string',
            'driver_surnames' => 'nullable|string',
            'driver_document' => 'nullable|string',
            'vehicle_placa' => 'nullable|string',
            'driver_licencia' => 'nullable|string',
            'nro_paquetes' => 'nullable|integer',
            'transbordo' => 'nullable|boolean',
            'cod_motive' => 'nullable|string',
            'net_weight' => 'nullable|numeric',
            'ubigeo_end' => 'nullable|string',
            'address_end' => 'nullable|string',
            'ubigeo_start' => 'nullable|string',
            'address_start' => 'nullable|string',
            'observation' => 'nullable|string',
            'factura' => 'nullable|string',
            'details' => 'nullable|array',
            'details.*.code' => 'required|string',
            'details.*.description' => 'required|string',
            'details.*.unit' => 'required|string',
            'details.*.quantity' => 'required|integer',
            'details.*.weight' => 'required|numeric',
        ];
    }
}
