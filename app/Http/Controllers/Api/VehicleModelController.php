<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleModelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/vehicleModel",
     *     summary="List of vehicle models",
     *     tags={"VehicleModel"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="brand_id", in="query", description="Brand ID", required=false, @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="List of vehicle models", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/VehicleModel"))),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(type="object", ref="#/components/schemas/ValidationError")),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(type="object", ref="#/components/schemas/Unauthenticated")),
     * )
     */
    public function index(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'brand_id' => [
                'nullable',
                'integer',
                Rule::exists('brands', 'id')->where('type', 'vehicle')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $idBrand = $request->query('brand_id');

        if ($idBrand === null) {
            $vehicleModels = VehicleModel::all();
            return response()->json($vehicleModels);
        }

        $vehicleModels = VehicleModel::where('brand_id', $idBrand)->get();
        return response()->json($vehicleModels);
    }


    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/vehicleModel",
     *     summary="Create a vehicle model",
     *     tags={"VehicleModel"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( required=true, description="VehicleModel data", @OA\JsonContent(ref="#/components/schemas/VehicleModelRequest")),
     *     @OA\Response( response=200, description="Vehicle model created", @OA\JsonContent(ref="#/components/schemas/VehicleModel")),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(type="object", ref="#/components/schemas/ValidationError")),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(type="object", ref="#/components/schemas/Unauthenticated"))
     * )
     *
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('vehicle_models', 'name')->where('brand_id', $request->input('brand_id'))
                    ->whereNull('deleted_at'),

            ],
            'brand_id' => 'required|integer|exists:brands,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'brand_id' => $request->input('brand_id'),
        ];

        $vehicleModel = VehicleModel::create($data);
        $vehicleModel = VehicleModel::with('brand')->find($vehicleModel->id);

        return response()->json($vehicleModel);
    }


    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/vehicleModel/{id}",
     *     summary="Show a vehicle model",
     *     tags={"VehicleModel"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Vehicle model ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Vehicle model", @OA\JsonContent(ref="#/components/schemas/VehicleModel")),
     *     @OA\Response( response=404, description="Vehicle model not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Vehicle model not found"))),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(type="object", ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function show(int $id)
    {
        $vehicleModel = VehicleModel::find($id);

        if ($vehicleModel === null) {
            return response()->json(['message' => 'Vehicle model not found'], 404);
        }

        return response()->json($vehicleModel);
    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/vehicleModel/{id}",
     *     summary="Update a vehicle model",
     *     tags={"VehicleModel"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Vehicle model ID", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, description="VehicleModel data", @OA\JsonContent(ref="#/components/schemas/VehicleModelRequest")),
     *     @OA\Response( response=200, description="Vehicle model updated", @OA\JsonContent(ref="#/components/schemas/VehicleModel")),
     *     @OA\Response( response=404, description="Vehicle model not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Vehicle model not found"))),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(type="object", ref="#/components/schemas/ValidationError")),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(type="object", ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function update(Request $request, int $id)
    {
        $vehicleModel = VehicleModel::find($id);

        if ($vehicleModel === null) {
            return response()->json(['message' => 'Vehicle model not found'], 404);
        }

        $validator = validator($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('vehicle_models', 'name')->where('brand_id', $request->input('brand_id'))
                    ->whereNull('deleted_at')->ignore($vehicleModel->id),
            ],
            'brand_id' => 'required|integer|exists:brands,id',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'brand_id' => $request->input('brand_id'),
        ];

        $vehicleModel->update($data);
        $vehicleModel = VehicleModel::with('brand')->find($vehicleModel->id);

        return response()->json($vehicleModel);
    }

    /**
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/vehicleModel/{id}",
     *     summary="Delete a vehicle model",
     *     tags={"VehicleModel"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Vehicle model ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Vehicle model deleted", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Vehicle model deleted"))),
     *     @OA\Response( response=404, description="Vehicle model not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Vehicle model not found"))),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(type="object", ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function destroy(int $id)
    {
        $vehicleModel = VehicleModel::find($id);

        if ($vehicleModel === null) {
            return response()->json(['message' => 'Vehicle model not found'], 404);
        }

        $vehicleModel->delete();

        return response()->json(['message' => 'Vehicle model deleted']);
    }
}
