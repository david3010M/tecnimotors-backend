<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleModelController extends Controller
{
    public function index(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'brand_id' => 'required|integer|exists:brands,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $idBrand = $request->query('brand_id');

        $vehicleModels = VehicleModel::where('brand_id', $idBrand)->get();
        return response()->json($vehicleModels);
    }

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


    public function show(int $id)
    {
        $vehicleModel = VehicleModel::find($id);

        if ($vehicleModel === null) {
            return response()->json(['message' => 'Vehicle model not found'], 404);
        }

        return response()->json($vehicleModel);
    }

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
