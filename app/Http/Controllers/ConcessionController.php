<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestConcession;
use App\Http\Resources\ConcessionResource;
use App\Models\Concession;
use App\Http\Requests\StoreConcessionRequest;
use App\Http\Requests\UpdateConcessionRequest;
use App\Models\RouteImages;

class ConcessionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/concession",
     *     tags={"Concession"},
     *     summary="Get all concessions",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="page", in="query", description="Número de página", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Cantidad de elementos por página", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="direction", in="query", description="Dirección de la ordenación", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(name="concession", in="query", description="Nombre de la concesion", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="client_id", in="query", description="Id del cliente", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="concessionaire_id", in="query", description="Id del concesionario", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Concessions", @OA\JsonContent(ref="#/components/schemas/ConcessionCollection")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexRequestConcession $request)
    {
        return $this->getFilteredResults(
            Concession::class,
            $request,
            Concession::filters,
            Concession::sorts,
            ConcessionResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/concession",
     *     tags={"Concession"},
     *     summary="Store a new concession",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/StoreConcessionRequest")
     *          )
     *      ),
     *     @OA\Response(response=200, description="Concession created", @OA\JsonContent(ref="#/components/schemas/ConcessionResource")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     *
     */
    public function store(StoreConcessionRequest $request)
    {
        $file = $request->file('logo');
        if (!$file) return response()->json(['message' => 'Logo is required'], 422);
        $concession = Concession::create($request->validated());
        $currentTime = now();
        $originalName = str_replace(' ', '_', $file->getClientOriginalName());

        $filename = $currentTime->format('YmdHis') . '_' . $originalName;
        $path = $file->storeAs('public/concessions', $filename);
        $routeImage = 'https://develop.garzasoft.com/tecnimotors-backend/storage/app/' . $path;
//        $routeImage = 'https://localhost/tecnimotors-backend/storage/app/' . $path;

        $dataImage = [
            'route' => $routeImage,
            'concession_id' => $concession->id,
        ];
        RouteImages::create($dataImage);

        return response()->json(new ConcessionResource($concession));
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/concession/{id}",
     *     tags={"Concession"},
     *     summary="Get a concession",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Concession id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Concession", @OA\JsonContent(ref="#/components/schemas/ConcessionResource")),
     *     @OA\Response(response=404, description="Concession not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Concession not found")))
     * )
     *
     */
    public function show(int $id)
    {
        $concession = Concession::find($id);
        if (!$concession) return response()->json(['message' => 'Concession not found'], 404);
        return response()->json(new ConcessionResource($concession));
    }

    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/concession/{id}",
     *     tags={"Concession"},
     *     summary="Update a concession",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Concession id", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/UpdateConcessionRequest")
     *          )
     *      ),
     *     @OA\Response(response=200, description="Concession updated", @OA\JsonContent(ref="#/components/schemas/ConcessionResource")),
     *     @OA\Response(response=404, description="Concession not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Concession not found"))),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     *
     */
    public function update(UpdateConcessionRequest $request, int $id)
    {
        $concession = Concession::find($id);
        if (!$concession) return response()->json(['message' => 'Concession not found'], 404);
        $file = $request->file('logo');
        if ($file) {
            $routeImagePrevious = RouteImages::where('concession_id', $concession->id)->first();
            if ($routeImagePrevious) {
                $path = storage_path(explode('https://develop.garzasoft.com/tecnimotors-backend/storage/', $routeImagePrevious->route)[1]);
                if (file_exists($path)) unlink($path);
                $routeImagePrevious->delete();
            }

            $currentTime = now();
            $originalName = str_replace(' ', '_', $file->getClientOriginalName());

            $filename = $currentTime->format('YmdHis') . '_' . $originalName;
            $path = $file->storeAs('public/concessions', $filename);
            $routeImage = 'https://develop.garzasoft.com/tecnimotors-backend/storage/app/' . $path;
//            $routeImage = 'http://localhost/tecnimotors-backend/storage/app/' . $path;

            $dataImage = [
                'route' => $routeImage,
                'concession_id' => $concession->id,
            ];
            RouteImages::create($dataImage);
        }

        $concession->update($request->validated());
        $concession = Concession::find($id);
        return response()->json(new ConcessionResource($concession));
    }

    /**
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/concession/{id}",
     *     tags={"Concession"},
     *     summary="Delete a concession",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Concession id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Concession deleted", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Concession deleted"))),
     *     @OA\Response(response=404, description="Concession not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Concession not found")))
     * )
     */
    public function destroy(int $id)
    {
        $concession = Concession::find($id);
        if (!$concession) return response()->json(['message' => 'Concession not found'], 404);
        $routeImagePrevious = RouteImages::where('concession_id', $concession->id)->first();
        $path = storage_path(explode('https://develop.garzasoft.com/tecnimotors-backend/storage/', $routeImagePrevious->route)[1]);
        if (file_exists($path)) unlink($path);
        if ($routeImagePrevious) $routeImagePrevious->delete();
        $concession->delete();
        return response()->json(['message' => 'Concession deleted']);
    }
}
