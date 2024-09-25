<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ocupation;
use App\Models\Person;
use App\Models\Specialty;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    /**
     * Get all Workers
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/worker",
     *     tags={"Worker"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Workers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Worker")
     *         )
     *     ),
     *       @OA\Parameter(
     *         name="speciality_id",
     *         in="query",
     *         description="ID of the Specialty to filter Workers",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *       @OA\Parameter(
     *         name="occupation",
     *         in="query",
     *         description="Occupation of the Workers",
     *         required=false,
     *               example="Mecanico"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        $speciality_id = $request->input('speciality_id') ?? '';
        $occupation = $request->input('occupation') ?? '';

        // Consulta base
        $query = Worker::query();

        // Filtro por occupation si se proporciona
        if ($occupation != '') {
            $query->whereRaw('LOWER(occupation) = ?', [strtolower($occupation)]);
        }

        // Filtro por specialty si se proporciona y existe
        // if ($speciality_id != '') {
        //     $specialty = Specialty::find($speciality_id);
        //     if (!$specialty) {
        //         return response()->json(['message' => 'Specialty not found'], 404);
        //     }

        //     $query->whereHas('specialties', function ($query) use ($speciality_id) {
        //         $query->where('specialties.id', $speciality_id);
        //     });
        // }

        // Ejecución de la consulta con paginación y carga de relaciones
        $workers = $query->with(['person','ocupation'])->where('state', true)->simplePaginate(50);

        return response()->json($workers);
    }

    /**
     * Show the specified Worker
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/worker/{id}",
     *     tags={"Worker"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Worker",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Worker found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Worker not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

    public function show(int $id)
    {

        $object = Worker::with(['person','ocupation', 'specialties'])->find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Worker not found'], 404
        );

    }

    /**
     * @OA\Post(
     *      path="/tecnimotors-backend/public/api/worker",
     *      summary="Store a new worker",
     *      tags={"Worker"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"person_id"},
     *              @OA\Property(property="startDate", type="string", example=null, description="StartDate of the worker"),
     *              @OA\Property(property="birthDate", type="string", example=null, description="BirthDate of the worker"),
     *               @OA\Property(property="occupation", type="string", example="-", description="Occupationi of the worker"),
     *              @OA\Property(property="person_id", type="integer", example="1", description="Worker ID")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created",
     *          @OA\JsonContent(ref="#/components/schemas/Worker")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *      )
     * )
     */

    public function store(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'person_id' => 'required|exists:people,id',
            'ocupation_id' => 'required|exists:ocupations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $ocupation = Ocupation::find($request->input('ocupation_id'));

        $data = [
            'startDate' => $request->input('startDate'),
            'birthDate' => $request->input('birthDate'),
            'occupation' => $ocupation->name ?? '-',
            'person_id' => $request->input('person_id'),
            'ocupation_id' => $request->input('ocupation_id'),

        ];

        $object = Worker::create($data);
        $object = Worker::with(['person','ocupation'])->find($object->id);
        return response()->json($object, 200);

    }

    /**
     * @OA\Post(
     *      path="/tecnimotors-backend/public/api/storeByOccupation",
     *      summary="Store a new person",
     *      tags={"Worker"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"typeofDocument","documentNumber"},
     *              @OA\Property(property="typeofDocument", type="string", example="DNI", description="Type of Document"),
     *              @OA\Property(property="documentNumber", type="string", example="12345678", description="Document Number"),
     *              @OA\Property(property="names", type="string", example="John Doe", description="Names"),
     *              @OA\Property(property="fatherSurname", type="string", example="Doe", description="Father's Surname"),
     *              @OA\Property(property="motherSurname", type="string", example="Smith", description="Mother's Surname"),
     *              @OA\Property(property="businessName", type="string", example="Doe Enterprises", description="Business Name"),
     *              @OA\Property(property="representativeDni", type="string", example="87654321", description="Representative's DNI"),
     *              @OA\Property(property="representativeNames", type="string", example="Jane Doe", description="Representative's Names"),
     *              @OA\Property(property="address", type="string", example="123 Main St", description="Address"),
     *              @OA\Property(property="phone", type="string", example="+123456789", description="Phone Number"),
     *              @OA\Property(property="email", type="string", example="example@example.com", description="Email"),
     *              @OA\Property(property="origin", type="string", example="USA", description="Origin"),
     *              @OA\Property(property="occupation", type="string", example="Asesor", description="Occupation"),
     *              @OA\Property(property="startDate", type="string", format="date", example="2023-01-01", description="Start Date"),
     *              @OA\Property(property="birthDate", type="string", format="date", example="1990-01-01", description="Birth Date"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Person created",
     *          @OA\JsonContent(ref="#/components/schemas/Person")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Validation error message.")
     *          )
     *      )
     * )
     */

    public function storeByOccupation(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'typeofDocument' => 'required',
            'documentNumber' => [
                'required',
                Rule::unique('people')->whereNull('deleted_at'),
            ],
            // 'occupation' => 'required|in:Cajero,Mecanico,Asesor',
            'ocupation_id' => 'required|exists:ocupations,id',
        ]);
        

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $ocupation = Ocupation::find($request->input('ocupation_id'));

        $data = [
            'typeofDocument' => $request->input('typeofDocument'),
            'documentNumber' => $request->input('documentNumber'),
            'address' => $request->input('address') ?? null,
            'phone' => $request->input('phone') ?? null,
            'email' => $request->input('email') ?? null,
            'origin' => $request->input('origin') ?? null,

            'names' => null,
            'fatherSurname' => null,
            'motherSurname' => null,
            'businessName' => null,
            'representativeDni' => null,
            'representativeNames' => null,
        ];

        if ($request->input('typeofDocument') == 'DNI') {
            $data['names'] = $request->input('names') ?? null;
            $data['fatherSurname'] = $request->input('fatherSurname') ?? null;
            $data['motherSurname'] = $request->input('motherSurname') ?? null;
        } elseif ($request->input('typeofDocument') == 'RUC') {
            $data['businessName'] = $request->input('businessName') ?? null;
            $data['representativeDni'] = $request->input('representativeDni') ?? null;
            $data['representativeNames'] = $request->input('representativeNames') ?? null;
        }

        $person = Person::create($data);
        $person = Person::find($person->id);

        $dataWorker = [
            'startDate' => $request->input('startDate') ?? null,
            'birthDate' => $request->input('birthDate') ?? null,
            'occupation' => $ocupation->name ?? null,
            'ocupation_id' => $ocupation->id,
            'person_id' => $person->id,
        ];

        $worker = Worker::create($dataWorker);
        $worker = Worker::with(['person','ocupation'])->find($worker->id);

        return response()->json($worker, 201);
    }

    /**
     * @OA\Put(
     *      path="/tecnimotors-backend/public/api/updateByOccupation/{id}",
     *      summary="Update an existing person by occupation",
     *      tags={"Worker"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the person to update",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"typeofDocument","documentNumber"},
     *              @OA\Property(property="typeofDocument", type="string", example="DNI", description="Type of Document"),
     *              @OA\Property(property="documentNumber", type="string", example="12345678", description="Document Number"),
     *              @OA\Property(property="names", type="string", example="John Doe", description="Names"),
     *              @OA\Property(property="fatherSurname", type="string", example="Doe", description="Father's Surname"),
     *              @OA\Property(property="motherSurname", type="string", example="Smith", description="Mother's Surname"),
     *              @OA\Property(property="businessName", type="string", example="Doe Enterprises", description="Business Name"),
     *              @OA\Property(property="representativeDni", type="string", example="87654321", description="Representative's DNI"),
     *              @OA\Property(property="representativeNames", type="string", example="Jane Doe", description="Representative's Names"),
     *              @OA\Property(property="address", type="string", example="123 Main St", description="Address"),
     *              @OA\Property(property="phone", type="string", example="+123456789", description="Phone Number"),
     *              @OA\Property(property="email", type="string", example="example@example.com", description="Email"),
     *              @OA\Property(property="origin", type="string", example="USA", description="Origin"),
     *              @OA\Property(property="occupation", type="string", example="Asesor", description="Occupation"),
     *              @OA\Property(property="startDate", type="string", format="date", example="2023-01-01", description="Start Date"),
     *              @OA\Property(property="birthDate", type="string", format="date", example="1990-01-01", description="Birth Date"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Person updated",
     *          @OA\JsonContent(ref="#/components/schemas/Person")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Person not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Person not found.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Validation error message.")
     *          )
     *      )
     * )
     */

    public function updateByOccupation(Request $request, $id)
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return response()->json(['message' => 'Worker not found.'], 404);
        }

        $person = Person::find($worker->person_id);

        if (!$person) {
            return response()->json(['message' => 'Person not found.'], 404);
        }

        $validator = validator()->make($request->all(), [
            'typeofDocument' => 'required',
            'documentNumber' => [
                'required',
                Rule::unique('people')->ignore($person->id)->whereNull('deleted_at'),
            ],
            // 'occupation' => 'required|in:Cajero,Mecanico,Asesor',
            'ocupation_id' => 'required|exists:ocupations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'typeofDocument' => $request->input('typeofDocument'),
            'documentNumber' => $request->input('documentNumber'),
            'address' => $request->input('address') ?? null,
            'phone' => $request->input('phone') ?? null,
            'email' => $request->input('email') ?? null,
            'origin' => $request->input('origin') ?? null,
            'occupation' => $request->input('occupation') ?? null,
            'names' => null,
            'fatherSurname' => null,
            'motherSurname' => null,
            'businessName' => null,
            'representativeDni' => null,
            'representativeNames' => null,
            'ocupation_id' => $request->input('ocupation_id') ?? null,
        ];

        if ($request->input('typeofDocument') == 'DNI') {
            $data['names'] = $request->input('names') ?? null;
            $data['fatherSurname'] = $request->input('fatherSurname') ?? null;
            $data['motherSurname'] = $request->input('motherSurname') ?? null;
        } elseif ($request->input('typeofDocument') == 'RUC') {
            $data['businessName'] = $request->input('businessName') ?? null;
            $data['representativeDni'] = $request->input('representativeDni') ?? null;
            $data['representativeNames'] = $request->input('representativeNames') ?? null;
        }

        $person->update($data);

        $worker = Worker::where('person_id', $person->id)->first();

        if (!$worker) {
            return response()->json(['message' => 'Worker not found.'], 404);
        }
        $ocupation = Ocupation::find($request->input('ocupation_id'));

        $dataWorker = [
            'startDate' => $request->input('startDate') ?? null,
            'birthDate' => $request->input('birthDate') ?? null,
            'occupation' => $ocupation->name ?? null,
            'ocupation_id' => $ocupation->id,
        ];

        $worker->update($dataWorker);

        $worker = Worker::with(['person','ocupation'])->find($worker->id);

        return response()->json($worker, 200);
    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/worker/{id}",
     *     summary="Update worker by ID",
     *     tags={"Worker"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of worker",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"person_id"},
     *              @OA\Property(property="startDate", type="string", example=null, description="StartDate of the worker"),
     *              @OA\Property(property="birthDate", type="string", example=null, description="BirthDate of the worker"),
     *               @OA\Property(property="occupation", type="string", example="-", description="Occupationi of the worker"),
     *              @OA\Property(property="person_id", type="integer", example="1", description="Worker ID")
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/Worker")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User  not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),

     * )
     *
     */
    public function update(Request $request, string $id)
    {

        $object = Worker::find($id);

        if (!$object) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }
        $validator = validator()->make($request->all(), [
            'person_id' => 'required|exists:people,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        $ocupation = Ocupation::find($request->input('ocupation_id'));

        $data = [
            'startDate' => $request->input('startDate'),
            'birthDate' => $request->input('birthDate'),
            'occupation' => $ocupation->name ?? '',
            'occupation_id' => $ocupation->id,
            'person_id' => $request->input('person_id'),
        ];

        $object->update($data);
        $object = Worker::with(['person','ocupation'])->find($object->id);
        return response()->json($object, 200);
    }

    /**
     * Remove the specified Worker
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/worker/{id}",
     *     tags={"Worker"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Worker",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Worker deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Worker deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Worker not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),

     * )
     *
     */
    public function destroy(int $id)
    {
        $object = Worker::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Worker not found'], 404
            );
        }

        //REVISAR ASOCIACIONES
        $object->state = false;
        $object->save();

        $object = Person::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Person not found'], 404
            );
        }

        //REVISAR ASOCIACIONES
        $object->state = false;
        $object->save();

    }
}
