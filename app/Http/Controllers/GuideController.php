<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestGuide;
use App\Http\Resources\GuideResource;
use App\Models\Guide;
use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;
use App\Models\GuideDetail;

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
        $request->merge([
            'number' => $number,
            'full_number' => 'T002-' . $number,
            'user_id' => $request->user()->id,
        ]);
        $guide = Guide::create($request->validated());

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
        $guide->update($request->validated());
        $details = $request->details;
        if ($details) {
            $guide->details()->delete();
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
