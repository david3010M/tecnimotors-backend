<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Get all suppliers
     *
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/supplier",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example="1"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Supplier")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/supplier?page=1"),
     *             @OA\Property(property="from", type="integer", example="1"),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/supplier?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/supplier"),
     *             @OA\Property(property="per_page", type="integer", example="15"),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example="15"),
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Supplier::with('person')->simplePaginate(15));
    }

    /**
     * Store a newly created supplier in storage.
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/supplier",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SupplierRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
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
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The date field is required.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'date' => 'required|date',
            'category' => 'required|string',
            'person_id' => [
                'required',
                'integer',
                'exists:people,id',
                Rule::unique('suppliers', 'person_id')->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'date' => $request->input('date'),
            'category' => $request->input('category'),
            'person_id' => $request->input('person_id')
        ];

        $supplier = Supplier::create($data);
        $supplier = Supplier::with('person')->find($supplier->id);

        return response()->json($supplier);
    }

    /**
     * Display the specified supplier.
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
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
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
        $supplier = Supplier::with('person')->find($id);

        return response()->json($supplier);
    }

    /**
     * Update the specified supplier in storage.
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SupplierRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
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
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The date field is required.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'date' => 'required|date',
            'category' => 'required|string',
            'person_id' => [
                'required',
                'integer',
                'exists:people,id',
                Rule::unique('suppliers', 'person_id')->ignore($supplier->id)->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'date' => $request->input('date'),
            'category' => $request->input('category'),
            'person_id' => $request->input('person_id')
        ];

        $supplier->update($data);
        $supplier = Supplier::with('person')->find($supplier->id);

        return response()->json($supplier);
    }

    public function destroy(int $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted']);
    }
}
