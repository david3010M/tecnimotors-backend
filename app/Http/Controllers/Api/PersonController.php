<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    /**
     * Get all Persons
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/person",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Persons",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Person")
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

    public function index(Request $request)
    {
        $search = $request->query('search');
        $ocupation = $request->query('ocupation', 'cliente');

        $category = $request->query('category');
        $getAll = filter_var($request->query('all', false), FILTER_VALIDATE_BOOLEAN);


        // Base query
        $query = Person::where('id', '!=', 1)
            ->whereNull('deleted_at')
            //->whereDoesntHave('worker') // <-- si tu relación es hasMany, usa 'workers'
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('documentNumber', 'like', '%' . $search . '%')
                        ->orWhere('names', 'like', '%' . $search . '%')
                        ->orWhere('fatherSurname', 'like', '%' . $search . '%')
                        ->orWhere('motherSurname', 'like', '%' . $search . '%')
                        ->orWhere('businessName', 'like', '%' . $search . '%');
                });
            })
            ->when(!empty($ocupation), function ($q) use ($ocupation) {
                $q->whereRaw('LOWER(ocupation) LIKE ?', ['%' . strtolower($ocupation) . '%']);
            })
            ->when(!empty($category), function ($q) use ($category) {
                $q->whereRaw('LOWER(category) LIKE ?', ['%' . strtolower($category) . '%']);
            });


        if ($getAll) {
            $persons = $query->get();

            return response()->json([
                'total' => $persons->count(),
                'data' => $persons,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => $persons->count(),
                'pagination' => null,
                'first_page_url' => null,
                'from' => $persons->isEmpty() ? null : 1,
                'next_page_url' => null,
                'path' => $request->url(),
                'prev_page_url' => null,
                'to' => $persons->isEmpty() ? null : $persons->count(),
                'page' => 1,
            ], 200);
        }

        // Paginación
        $perPage = $request->query('per_page', 100);
        $currentPage = $request->query('page', 1);

        $persons = $query->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'total' => $persons->total(),
            'data' => $persons->items(),
            'current_page' => $persons->currentPage(),
            'last_page' => $persons->lastPage(),
            'per_page' => $persons->perPage(),
            'pagination' => $perPage,
            'first_page_url' => $persons->url(1),
            'from' => $persons->firstItem(),
            'next_page_url' => $persons->nextPageUrl(),
            'path' => $persons->path(),
            'prev_page_url' => $persons->previousPageUrl(),
            'to' => $persons->lastItem(),
            'page' => $currentPage,
        ], 200);
    }



    /**
     * @OA\Post(
     *      path="/tecnimotors-backend/public/api/person",
     *      summary="Store a new person",
     *      tags={"Person"},
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
     *              @OA\Property(property="ocupation", type="string", example="Engineer", description="Occupation")
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
     *          response=404,
     *          description="Person not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Person not found")
     *          )
     *      )
     * )
     */

    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'typeofDocument' => 'required',
            'documentNumber' => 'required',
            'ocupation' => 'nullable|in:cliente,proveedor', // ✅ solo acepta cliente o proveedor
        ], [
            'ocupation.required' => 'El campo ocupación es obligatorio.',
            'ocupation.in' => 'La ocupación debe ser cliente o proveedor.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $occupation = strtolower($request->input('ocupation', 'cliente')); // normalizar a minúsculas

        // Buscar persona por documento
        $person = Person::where('documentNumber', $request->input('documentNumber'))
            ->whereNull('deleted_at')
            ->first();

        if (!$person) {
            // Crear persona nueva
            $data = [
                'typeofDocument' => $request->input('typeofDocument'),
                'documentNumber' => $request->input('documentNumber'),
                'address' => $request->input('address') ?? null,
                'phone' => $request->input('phone') ?? null,
                'email' => $request->input('email') ?? null,
                'origin' => $request->input('origin') ?? null,
                'ocupation' => $occupation, // ocupación desde el request
                'category' => $request->input('category') ?? null,
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
        } else {
            // Si ya existe, concatenar la ocupación si no la tiene
            $ocupations = explode(',', $person->ocupation ?? '');
            $ocupations = array_map('trim', $ocupations);

            if (!in_array($occupation, $ocupations)) {
                $ocupations[] = $occupation;
                $person->ocupation = implode(',', $ocupations);
                $person->save();
            }
        }

        return response()->json($person, 200);
    }



    /**
     * Show the specified Person
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/person/{id}",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Person",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Person not found"
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
     */

    public function show(int $id)
    {

        $object = Person::find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Person not found'],
            404
        );

    }

    /**
     * Get all vehicles associated with a specific Person
     *
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/person/{id}/vehicles",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Person",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of vehicles associated with the Person",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Vehicle")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Person not found"
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
    public function vehiclesByPerson(int $id)
    {
        // Encuentra la persona por su ID
        $person = Person::find($id);

        if ($person) {
            // Devuelve los vehículos asociados a la persona
            $vehicles = $person->vehicles()->with('typeVehicle', 'vehicleModel', 'attentions.budgetSheet')->get();
            return response()->json([
                'data' => $vehicles,
            ], 200);
        }

        return response()->json(
            ['message' => 'Person not found'],
            404
        );
    }


    /**
     * Get all pending attentions associated with a specific Person
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/person/{id}/attentions",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID of the Person", @OA\Schema( type="integer" ) ),
     *     @OA\Response(response=200, description="List of attentions associated with the Person",
     *          @OA\JsonContent( @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AttentionByPerson")) ) ),
     *     @OA\Response(response=404, description="Person not found",
     *          @OA\JsonContent( @OA\Property(property="message", type="string", example="Person not found") ) ),
     *     @OA\Response(response=401, description="Unauthorized",
     *          @OA\JsonContent( @OA\Property(property="message", type="string", example="Unauthenticated") ) )
     * )
     */
    public function attentionsByPerson(int $id)
    {
        $person = Person::find($id);

        if ($person) {
            $attentions = $person->attentions()->get();
            return response()->json(['data' => $attentions]);
        }

        return response()->json(
            ['message' => 'Person not found'],
            404
        );
    }


    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/person/{id}",
     *     summary="Update person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of person",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
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
     *              @OA\Property(property="ocupation", type="string", example="Engineer", description="Occupation")
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Person or User not found",
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
        $object = Person::find($id);

        if (!$object) {
            return response()->json(
                ['message' => 'User not found'],
                404
            );
        }

        // Si no envía occupation, asumimos "cliente"
        $occupation = strtolower($request->input('occupation', 'cliente'));

        $validator = validator()->make($request->all(), [
            'typeofDocument' => 'required',
            'documentNumber' => 'required', // quitamos unique
            'occupation' => 'nullable|in:cliente,proveedor', // ahora puede no enviarse
        ], [
            'occupation.in' => 'La ocupación debe ser cliente o proveedor.',
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
            'category' => $request->input('category') ?? null,
        ];

        if ($request->input('typeofDocument') == 'DNI') {
            $data['names'] = $request->input('names') ?? null;
            $data['fatherSurname'] = $request->input('fatherSurname') ?? null;
            $data['motherSurname'] = $request->input('motherSurname') ?? null;
            $data['businessName'] = null;
            $data['representativeDni'] = null;
            $data['representativeNames'] = null;
        } elseif ($request->input('typeofDocument') == 'RUC') {
            $data['names'] = null;
            $data['fatherSurname'] = null;
            $data['motherSurname'] = null;
            $data['businessName'] = $request->input('businessName') ?? null;
            $data['representativeDni'] = $request->input('representativeDni') ?? null;
            $data['representativeNames'] = $request->input('representativeNames') ?? null;
        }

        $object->update($data);

        // ✅ Concatenar occupation ("cliente" por defecto)
        $ocupations = explode(',', $object->ocupation ?? '');
        $ocupations = array_map('trim', $ocupations);

        if (!in_array($occupation, $ocupations)) {
            $ocupations[] = $occupation;
            $object->ocupation = implode(',', $ocupations);
            $object->save();
        }

        return response()->json($object, 200);
    }


    /**
     * Remove the specified Person
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/person/{id}",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Person",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Person deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Person not found"
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
    public function destroy(int $id, string $occupation = 'cliente')
    {
        $object = Person::with('vehicles.attentions')->find($id);

        if (!$object) {
            return response()->json(['message' => 'Person not found'], 404);
        }

        // Normalizamos la ocupación
        $occupation = strtolower(trim($occupation));

        // Manejo de ocupations en la persona
        $ocupations = explode(',', $object->ocupation ?? '');
        $ocupations = array_filter(array_map('trim', $ocupations)); // limpiar espacios y vacíos

        // Quitar la ocupación solicitada
        $ocupations = array_filter($ocupations, fn($o) => strtolower($o) !== $occupation);

        if (!empty($ocupations)) {
            // Si quedan otras ocupaciones, solo actualizamos y no eliminamos
            $object->ocupation = implode(',', $ocupations);
            $object->save();

            return response()->json([
                'message' => "Ocupación '{$occupation}' eliminada de la persona",
                'person' => $object
            ]);
        }

        // Si ya no queda ninguna ocupación, validar atenciones antes de borrar
        foreach ($object->vehicles as $vehicle) {
            if ($vehicle->attentions->count() > 0) {
                return response()->json(['message' => 'Person has attentions'], 409);
            }
        }

        //    $object->delete();

        return response()->json(['message' => 'Person deleted successfully']);
    }


}
