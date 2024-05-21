<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{

    public function index()
    {
        return response()->json(Brand::simplePaginate(15));
    }


    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('brands')->whereNull('deleted_at')
            ],
            'type' => 'required|string|in:vehicle,product'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'type' => $request->input('type')
        ];

        $brand = Brand::create($data);
        $brand = Brand::find($brand->id);

        return response()->json($brand);
    }

    public function show(int $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }
        return response()->json($brand);
    }

    public function update(Request $request, int $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('brands')->ignore($id)->whereNull('deleted_at')
            ],
            'type' => 'required|string|in:vehicle,product'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'type' => $request->input('type')
        ];

        $brand->update($data);
        $brand = Brand::find($brand->id);

        return response()->json($brand);

    }


    public function destroy(int $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

//        if ($brand->vehicles()->count() > 0) {
//            return response()->json(['error' => 'Brand has vehicles'], 422);
//        }
//
//        if ($brand->products()->count() > 0) {
//            return response()->json(['error' => 'Brand has products'], 422);
//        }

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
