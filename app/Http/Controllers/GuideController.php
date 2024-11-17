<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestGuide;
use App\Http\Resources\GuideResource;
use App\Models\District;
use App\Models\Guide;
use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;
use App\Models\GuideDetail;
use App\Models\GuideMotive;
use App\Models\Person;
use App\Models\Worker;

class GuideController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/guide",
     *     tags={"Guide"},
     *     summary="Listado de guías",
     *     description="Obtiene un listado de guías",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", description="Número de página", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Cantidad de elementos por página", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="number", in="query", description="Número de guía", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="recipient_names", in="query", description="Nombre del destinatario", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="guide_motive_id", in="query", description="ID del motivo de la guía", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="worker_id", in="query", description="ID del conductor", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="district_id_start", in="query", description="ID del distrito de inicio", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="district_id_end", in="query", description="ID del distrito de fin", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="observation", in="query", description="Observación", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Listado de guías", @OA\JsonContent(ref="#/components/schemas/GuideCollection")),
     *     @OA\Response(response="401", description="No autorizado"),
     * )
     *
     */
    public function index(IndexRequestGuide $request)
    {
        return $this->getFilteredResults(
            Guide::class,
            $request,
            Guide::filters,
            Guide::sorts,
            GuideResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/guide",
     *     tags={"Guide"},
     *     summary="Crear guía",
     *     description="Crea una guía",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreGuideRequest")),
     *     @OA\Response(response="200", description="Guía creada", @OA\JsonContent(ref="#/components/schemas/GuideResource")),
     *     @OA\Response(response="401", description="No autorizado"),
     *     @OA\Response(response="422", description="Error de validación")
     * )
     */
    public function store(StoreGuideRequest $request)
    {
        $number = $this->nextCorrelative(Guide::class, 'number');
        $recipient = Person::find($request->input('recipient_id'));
        $driver = Worker::find($request->input('worker_id'));
        if (!$driver) return response()->json(['message' => 'El conductor no existe'], 422);
        $motive = GuideMotive::find($request->input('guide_motive_id'));
        $districtStart = District::find($request->input('district_id_start'));
        $districtEnd = District::find($request->input('district_id_end'));
        $details = $request->input('details');
        $netWeight = 0;
        foreach ($details as $detail) {
            $netWeight += $detail['weight'];
        }
        $request->merge([
            'number' => $number,
            'full_number' => 'T002-' . $number,
            'motive_name' => $motive->name,
            'cod_motive' => $motive->code,
            'recipient_names' => $recipient->typeofDocument === 'DNI' ? $recipient->names . ' ' . $recipient->fatherSurname . ' ' . $driver->motherSurname : $recipient->businessName,
            'recipient_document' => $recipient->documentNumber,
            'driver_names' => $driver->person?->names,
            'driver_surnames' => $driver->person?->fatherSurname . ' ' . $driver->person?->motherSurname,
            'driver_document' => $driver->person?->documentNumber,
            'driver_licencia' => str_replace(" ", "", $request->input('driver_licencia')),
            'vehicle_placa' => str_replace(" ", "", $request->input('vehicle_placa')),
            'net_weight' => $netWeight,
            'ubigeo_start' => $districtStart->ubigeo_code,
            'ubigeo_end' => $districtEnd->ubigeo_code,
            'user_id' => $request->user()->id,
            'branch_id' => 1,
        ]);
        $guide = Guide::create($request->all());

        $details = $request->details;
        foreach ($details as $detail) {
            GuideDetail::create([
                'code' => $detail['code'],
                'description' => $detail['description'],
                'unit' => $detail['unit'],
                'quantity' => $detail['quantity'],
                'weight' => $detail['weight'],
                'guide_id' => $guide->id,
            ]);
        }

        $guide = Guide::find($guide->id);
        return response()->json(GuideResource::make($guide));
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/guide/{id}",
     *     tags={"Guide"},
     *     summary="Obtener guía",
     *     description="Obtiene una guía",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la guía", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Guía", @OA\JsonContent(ref="#/components/schemas/GuideResource")),
     *     @OA\Response(response="401", description="No autorizado"),
     *     @OA\Response(response="404", description="Guía no encontrada")
     * )
     */
    public function show(int $id)
    {
        $guide = Guide::find($id);
        if (!$guide) return response()->json(['message' => 'Guía no encontrada'], 404);
        return response()->json(GuideResource::make($guide));
    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/guide/{id}",
     *     tags={"Guide"},
     *     summary="Actualizar guía",
     *     description="Actualiza una guía",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la guía", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateGuideRequest")),
     *     @OA\Response(response="200", description="Guía actualizada", @OA\JsonContent(ref="#/components/schemas/GuideResource")),
     *     @OA\Response(response="401", description="No autorizado"),
     *     @OA\Response(response="404", description="Guía no encontrada"),
     *     @OA\Response(response="422", description="Error de validación")
     * )
     */
    public function update(UpdateGuideRequest $request, int $id)
    {
        $guide = Guide::find($id);
        if (!$guide) return response()->json(['message' => 'Guía no encontrada'], 404);
        if ($guide->status_facturado) return response()->json(['message' => 'No se puede modificar una guía facturada'], 422);
        $recipient = Person::find($request->input('recipient_id'));
        $driver = Person::find($request->input('worker_id'));
        $motive = GuideMotive::find($request->input('guide_motive_id'));
        $districtStart = District::find($request->input('district_id_start'));
        $districtEnd = District::find($request->input('district_id_end'));
        $details = $request->input('details');
        $netWeight = 0;
        foreach ($details as $detail) {
            $netWeight += $detail['weight'] * $detail['quantity'];
        }
        $request->merge([
            'motive_name' => $motive->name,
            'cod_motive' => $motive->code,
            'recipient_names' => $recipient->typeofDocument === 'DNI' ? $recipient->names . ' ' . $recipient->fatherSurname . ' ' . $driver->motherSurname : $recipient->businessName,
            'recipient_document' => $recipient->documentNumber,
            'driver_names' => $driver->names,
            'driver_surnames' => $driver->fatherSurname . ' ' . $driver->motherSurname,
            'driver_document' => $driver->documentNumber,
            'net_weight' => $netWeight,
            'ubigeo_start' => $districtStart->ubigeo_code,
            'ubigeo_end' => $districtEnd->ubigeo_code,
        ]);
        $guide->update($request->all());

        $guide->details()->delete();
        $details = $request->details;
        foreach ($details as $detail) {
            GuideDetail::create([
                'code' => $detail['code'],
                'description' => $detail['description'],
                'unit' => $detail['unit'],
                'quantity' => $detail['quantity'],
                'weight' => $detail['weight'],
                'guide_id' => $guide->id,
            ]);
        }

        $guide = Guide::find($guide->id);
        return response()->json(GuideResource::make($guide));
    }

    /**
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/guide/{id}",
     *     tags={"Guide"},
     *     summary="Eliminar guía",
     *     description="Elimina una guía",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la guía", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Guía eliminada"),
     *     @OA\Response(response="401", description="No autorizado"),
     *     @OA\Response(response="404", description="Guía no encontrada")
     * )
     */
    public function destroy(int $id)
    {
        $guide = Guide::find($id);
        if (!$guide) return response()->json(['message' => 'Guía no encontrada'], 404);
        if ($guide->status_facturado) return response()->json(['message' => 'No se puede eliminar una guía facturada'], 422);
        if ($guide->details()->count() > 0) return response()->json(['message' => 'No se puede eliminar una guía con detalles'], 422);
        $guide->delete();
        return response()->json(['message' => 'Guía eliminada']);
    }
}
