<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConceptMov;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConceptMovController extends Controller
{
    /**
     * Get all ConceptMovs with pagination
     *
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/conceptMov",
     *     tags={"ConceptMovs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example="1"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ConceptMov")),
     *             @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/conceptMov?page=1"),
     *             @OA\Property(property="from", type="integer", example="1"),
     *             @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/conceptMov?page=2"),
     *             @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/conceptMov"),
     *             @OA\Property(property="per_page", type="integer", example="15"),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example="1"),
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
     *
     */
    public function index()
    {
        return response()->json(ConceptMov::simplePaginate(15));
    }

    /**
     * Store a newly created ConceptMov in storage.
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/conceptMov",
     *     tags={"ConceptMovs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ConceptMovRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/ConceptMov")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The name has already been taken.")
     *          )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('concept_movs', 'name')->whereNull('deleted_at'),
            ],
            'typemov' => [
                'nullable',
                'string',
                Rule::in(['INGRESO', 'EGRESO']), // Asegura que el tipo sea uno de los valores permitidos
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'typemov' => $request->input('typemov'),
        ];

        $conceptMov = ConceptMov::create($data);
        $conceptMov = ConceptMov::find($conceptMov->id);

        return response()->json($conceptMov);
    }

    /**
     * Display the specified ConceptMov.
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/conceptMov/{id}",
     *     tags={"ConceptMovs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ConceptMov id",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/ConceptMov")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="ConceptMov not found")
     *          )
     *     )
     * )
     */
    public function show(int $id)
    {
        $conceptMov = ConceptMov::find($id);

        if (!$conceptMov) {
            return response()->json(['message' => 'ConceptMov not found'], 404);
        }

        return response()->json($conceptMov);
    }

    /**
     * Update the specified ConceptMov in storage.
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/conceptMov/{id}",
     *     tags={"ConceptMovs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ConceptMov id",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ConceptMovRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/ConceptMov")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="ConceptMov not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The name has already been taken.")
     *          )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $conceptMov = ConceptMov::find($id);

        if (!$conceptMov) {
            return response()->json(['message' => 'ConceptMov not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'typemov' => [
                'nullable',
                'string',
                Rule::in(['INGRESO', 'EGRESO']), // Solo acepta INGRESO o EGRESO
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'typemov' => $request->input('typemov') ?? $conceptMov->typemov,
        ];

        $conceptMov->update($data);
        $conceptMov = ConceptMov::find($conceptMov->id);

        return response()->json($conceptMov);
    }

    /**
     * Remove the specified ConceptMov from storage.
     * @OA\Delete(
     *     path="/tecnimotors-backend/public/api/conceptMov/{id}",
     *     tags={"ConceptMovs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ConceptMov id",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="ConceptMov deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="ConceptMov not found")
     *          )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $conceptMov = ConceptMov::find($id);

        if (!$conceptMov) {
            return response()->json(['message' => 'El concepto de movimiento no se encontró'], 404);
        }
        if ($conceptMov->docAlmacens()->count() > 0) {
            return response()->json(['message' => 'El concepto de movimiento no puede ser eliminado porque tiene documentos de almacén asociados'], 422);
        }

        $conceptMov->delete();

        return response()->json(['message' => 'El concepto de movimiento se eliminó correctamente']);
    }
}
