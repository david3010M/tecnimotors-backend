<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConceptPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConceptPayController extends Controller
{
    /**
     * Get all ConceptPays
     * @OA\Get (
     *      path="/tecnimotors-backend/public/api/conceptPay",
     *      tags={"ConceptPay"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="List of active ConceptPays",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ConceptPay")),
     *              @OA\Property(property="first_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/conceptPay?page=1"),
     *              @OA\Property(property="from", type="integer", example=1),
     *              @OA\Property(property="next_page_url", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/conceptPay?page=2"),
     *              @OA\Property(property="path", type="string", example="http://develop.garzasoft.com/tecnimotors-backend/public/api/conceptPay"),
     *              @OA\Property(property="per_page", type="integer", example=15),
     *              @OA\Property(property="prev_page_url", type="string", example="null"),
     *              @OA\Property(property="to", type="integer", example=15)
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     *  )
     */
    public function index()
    {
        return response()->json(ConceptPay::simplePaginate(15));
    }

    /**
     * Create a new ConceptPay
     * @OA\Post (
     *      path="/tecnimotors-backend/public/api/conceptPay",
     *      tags={"ConceptPay"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\JsonContent(ref="#/components/schemas/ConceptPayRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="ConceptPay created",
     *          @OA\JsonContent(ref="#/components/schemas/ConceptPay")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The name has already been taken.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     *  )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [

            'name' => [
                'required',
                'string',
                Rule::unique('concept_pays', 'name')->whereNull('deleted_at'),
            ],
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $tipo = 'CONC';
        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM attentions a WHERE SUBSTRING(number, 1, 4) = ?', [$tipo])[0]->siguienteNum;
        $siguienteNum = (int) $resultado;

        $data = [
            'number' => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'name' => $request->input('name'),
            'type' => $request->input('type'),
        ];

        $conceptPay = ConceptPay::create($data);
        $conceptPay = ConceptPay::find($conceptPay->id);

        return response()->json($conceptPay);
    }

    /**
     * Get a ConceptPay
     * @OA\Get (
     *      path="/tecnimotors-backend/public/api/conceptPay/{id}",
     *      tags={"ConceptPay"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ConceptPay Id",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="ConceptPay detail",
     *          @OA\JsonContent(ref="#/components/schemas/ConceptPay")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="ConceptPay not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="ConceptPay not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     *  )
     */
    public function show(int $id)
    {
        $conceptPay = ConceptPay::find($id);

        if (!$conceptPay) {
            return response()->json(['messsage' => 'ConceptPay not found'], 404);
        }

        return response()->json($conceptPay);
    }

    /**
     * Update a ConceptPay
     * @OA\Put (
     *      path="/tecnimotors-backend/public/api/conceptPay/{id}",
     *      tags={"ConceptPay"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ConceptPay Id",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          @OA\JsonContent(ref="#/components/schemas/ConceptPayRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="ConceptPay updated",
     *          @OA\JsonContent(ref="#/components/schemas/ConceptPay")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="ConceptPay not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="ConceptPay not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The name has already been taken.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     *  )
     */
    public function update(Request $request, int $id)
    {
        $conceptPay = ConceptPay::find($id);

        if (!$conceptPay) {
            return response()->json(['messsage' => 'ConceptPay not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'number' => 'required|integer',
            'name' => [
                'required',
                'string',
                Rule::unique('concept_pays', 'name')->whereNull('deleted_at')->ignore($conceptPay->id),
            ],
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'number' => $request->input('number'),
            'name' => $request->input('name'),
            'type' => $request->input('type'),
        ];

        $conceptPay->update($data);
        $conceptPay = ConceptPay::find($conceptPay->id);

        return response()->json($conceptPay);
    }

    /**
     * Delete a ConceptPay
     * @OA\Delete (
     *      path="/tecnimotors-backend/public/api/conceptPay/{id}",
     *      tags={"ConceptPay"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ConceptPay Id",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="ConceptPay deleted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="ConceptPay deleted")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="ConceptPay not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="ConceptPay not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     *  )
     */
    public function destroy(int $id)
    {
        $conceptPay = ConceptPay::find($id);

        if (!$conceptPay) {
            return response()->json(['messsage' => 'ConceptPay not found'], 404);
        }

        $conceptPay->delete();

        return response()->json(['message' => 'ConceptPay deleted']);
    }
}
