<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{

    /**
     * Get all Brands
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/brand",
     *     tags={"Brand"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Brands",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Brand")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/brand?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/brand?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/brand"),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", type="string", example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Brand::simplePaginate(15));
    }


    /**
     * Create a new Brand
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/brand",
     *     tags={"Brand"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "type"},
     *              @OA\Property(property="name", type="string", example="Brand 1"),
     *              @OA\Property(property="type", type="string", example="vehicle"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Brand")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     )
     * )
     */
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

    /**
     * Get a Brand
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/brand/{id}",
     *     tags={"Brand"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Brand",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand found",
     *         @OA\JsonContent(ref="#/components/schemas/Brand")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Brand not found.")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        return response()->json($brand);
    }

    /**
     * Update a Brand
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/brand/{id}",
     *     tags={"Brand"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Brand",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "type"},
     *              @OA\Property(property="name", type="string", example="Brand 1"),
     *              @OA\Property(property="type", type="string", example="vehicle"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Brand")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Brand not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
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

    /**
     * Delete a Brand
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/brand/{id}",
     *     tags={"Brand"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Brand",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Brand deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Brand not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Brand has asssoications",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Brand has vehicles")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

//        if ($brand->vehicles()->count() > 0) {
//            return response()->json(['error' => 'Brand has vehicles'], 409);
//        }
//
//        if ($brand->products()->count() > 0) {
//            return response()->json(['error' => 'Brand has products'], 409);
//        }

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
