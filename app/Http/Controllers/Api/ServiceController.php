<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Get all Services
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/service",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Services",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Service")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/service?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/service?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/service"),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Service::simplePaginate(50));
    }

    /**
     * Create a new Service
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/service",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ServiceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service created",
     *         @OA\JsonContent(ref="#/components/schemas/Service")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
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
                Rule::unique('services')->whereNull('deleted_at'),
            ],
            'quantity' => 'required|integer',
            'saleprice' => 'required|numeric',
            'time' => 'required|string',
            'period' => 'nullable|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'quantity' => $request->input('quantity'),
            'saleprice' => $request->input('saleprice'),
            'time' => $request->input('time'),
            'period' => $request->input('period'),
        ];

        $service = Service::create($data);
        $service = Service::find($service->id);

        return response()->json($service);
    }

    /**
     * Get a specific Service
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/service/{id}",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service found",
     *         @OA\JsonContent(ref="#/components/schemas/Service")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json($service);
    }

    /**
     * Update a specific Service
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/service/{id}",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ServiceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated",
     *         @OA\JsonContent(ref="#/components/schemas/Service")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('services')->ignore($id)->whereNull('deleted_at'),
            ],
            'quantity' => 'required|integer',
            'saleprice' => 'required|numeric',
            'time' => 'required|string',
            'period' => 'nullable|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'quantity' => $request->input('quantity'),
            'saleprice' => $request->input('saleprice'),
            'time' => $request->input('time'),
            'period' => $request->input('period'),
        ];

        $service->update($data);
        $service = Service::find($service->id);

        return response()->json($service);

    }

    /**
     * Delete a specific Service
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/service/{id}",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Service cannot be deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service cannot be deleted because it is related to a detail budget")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

//        if ($service->detailBudgets()->count() > 0) {
//            return response()->json(['message' => 'Service cannot be deleted because it is related to a detail budget'], 409);
//        }

        $service->delete();

        return response()->json(['message' => 'Service deleted']);
    }
}
