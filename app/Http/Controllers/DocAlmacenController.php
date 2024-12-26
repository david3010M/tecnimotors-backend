<?php

namespace App\Http\Controllers;

use App\Models\DocAlmacen;
use Illuminate\Http\Request;

class DocAlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/docalmacen",
     *     tags={"Doc Almacen"},
     *     summary="Get all documentos almacen",
     *     description="Get all documentos almacen",
     *     security={{"bearerAuth": {}}},

     *     @OA\Parameter( name="from", in="query", description="Filter by from", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter( name="to", in="query", description="Filter by to", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter( name="comment", in="query", description="Filter by comment", @OA\Schema(type="string")),
     *     @OA\Parameter( name="concepto_mov_name", in="query", description="Filter by concepto_mov_name", @OA\Schema(type="string")),
     *     @OA\Parameter( name="user_name", in="query", description="Filter by user_name", @OA\Schema(type="string")),
     *     @OA\Parameter( name="product_name", in="query", description="Filter by product_name", @OA\Schema(type="string")),



     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/DocAlmacen")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */

    public function index(Request $request)
    {
        // Obtención de los parámetros de la solicitud
        $page = $request->input('page', 1); // Página actual
        $perPage = $request->input('per_page', 15); // Cantidad de registros por página
        $start = $request->input('from', ''); // Fecha de inicio
        $end = $request->input('to', ''); // Fecha de fin
        $comment = $request->input('comment', ''); // Filtro por comentario (vacío por defecto)

        $userName = $request->input('user_name', ''); // Filtro por nombre de usuario
        $productName = $request->input('product_name', ''); // Filtro por nombre de producto
        $conceptoMovName = $request->input('concepto_mov_name', ''); // Filtro por nombre de concepto_mov

        // Consultar las amortizaciones con filtros
        $query = DocAlmacen::with(['user.worker.person', 'user.typeUser', 'concept_mov', 'product.category', 'product.unit', 'product.brand'])->query();

        // Filtrar por fecha de inicio si no está vacío
        if (!empty($start)) {
            $query->where('date_moviment', '>=', $start); // Filtrar por fecha de inicio
        }

        // Filtrar por fecha de fin si no está vacío
        if (!empty($end)) {
            $query->where('date_moviment', '<=', $end); // Filtrar por fecha de fin
        }

        // Filtrar por comentario si no está vacío
        if (!empty($comment)) {
            $query->where('comment', 'like', '%' . $comment . '%'); // Filtrar por comentario (en el campo 'sequentialNumber')
        }

        if (!empty($userName)) {
            $query->whereHas('user', function ($query) use ($userName) {
                $query->where('username', 'like', '%' . $userName . '%');
            });
        }

        if (!empty($productName)) {
            $query->whereHas('product', function ($query) use ($productName) {
                $query->where('name', 'like', '%' . $productName . '%');
            });
        }

        if (!empty($conceptoMovName)) {
            $query->whereHas('conceptoMov', function ($query) use ($conceptoMovName) {
                $query->where('name', 'like', '%' . $conceptoMovName . '%');
            });
        }

        // Paginación con los filtros aplicados
        $objects = $query->paginate($perPage, ['*'], 'page', $page);

        // Responder con la paginación estructurada en JSON
        return response()->json([
            'total' => $objects->total(), // Total de registros
            'data' => $objects->items(), // Los registros de la página actual
            'current_page' => $objects->currentPage(), // Página actual
            'last_page' => $objects->lastPage(), // Última página disponible
            'per_page' => $objects->perPage(), // Cantidad de registros por página
            'first_page_url' => $objects->url(1), // URL de la primera página
            'from' => $objects->firstItem(), // Primer registro de la página actual
            'next_page_url' => $objects->nextPageUrl(), // URL de la siguiente página
            'path' => $objects->path(), // Ruta base de la paginación
            'prev_page_url' => $objects->previousPageUrl(), // URL de la página anterior
            'to' => $objects->lastItem(), // Último registro de la página actual
        ]);
    }
    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/docalmacen",
     *     tags={"Doc Almacen"},
     *     summary="Create a new Doc Almacen",
     *     description="Store a new document in the Doc Almacen system",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date_moviment", "quantity", "user_id", "concept_mov_id", "product_id"},
     *             @OA\Property(property="date_moviment", type="string", format="date-time", example="2024-05-22 14:30:00"),
     *             @OA\Property(property="quantity", type="number", format="float", example="1500.75"),
     *             @OA\Property(property="comment", type="string", example="Pago de factura para el producto X"),
     *             @OA\Property(property="user_id", type="integer", example="4"),
     *             @OA\Property(property="concept_mov_id", type="integer", example="2"),
     *             @OA\Property(property="product_id", type="integer", example="5")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/DocAlmacen")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function store(Request $request)
    {
        // Validación de los datos entrantes
        $validatedData = $request->validate([
            'date_moviment' => 'required|date_format:Y-m-d H:i:s', // Validar fecha y hora
            'quantity' => 'required|numeric|gt:0', // Asegura que la cantidad sea mayor a 0
            'comment' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id', // Verifica si el usuario existe
            'concept_mov_id' => 'required|exists:concept_movements,id', // Verifica si el concepto de movimiento existe
            'product_id' => 'required|exists:products,id', // Verifica si el producto existe
        ]);

        $data = [
            'date_moviment' => $validatedData['date_moviment'],
            'quantity' => $validatedData['quantity'] ?? 1,
            'comment' => $validatedData['comment'] ?? null, // Asigna null si no se proporciona comentario
            'user_id' => $validatedData['user_id'],
            'concept_mov_id' => $validatedData['concept_mov_id'],
            'product_id' => $validatedData['product_id'],
        ];

        // Crear el nuevo documento de almacén
        $docAlmacen = DocAlmacen::create($data);

        $docAlmacen = DocAlmacen::with(['user.worker.person', 'user.typeUser', 'concept_mov', 'product.category', 'product.unit', 'product.brand'])
            ->find($docAlmacen->id);

        // Responder con el nuevo documento de almacén creado
        return response()->json($docAlmacen, 201);
    }

    /**
     * @OA\Get(
     *     path="/tecnimotors-backend/public/api/docalmacen/{id}",
     *     tags={"Doc Almacen"},
     *     summary="Get a Doc Almacen by ID",
     *     description="Retrieve a document from the Doc Almacen system by its ID",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the document to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/DocAlmacen")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="404", description="Doc Almacen not found")
     * )
     */
    public function show($id)
    {
        // Buscar el documento de almacén por ID
        $docAlmacen = DocAlmacen::with(['user.worker.person', 'user.typeUser', 'concept_mov', 'product.category', 'product.unit', 'product.brand'])
            ->find($id);

        // Si el documento no se encuentra, responder con un error 404
        if (!$docAlmacen) {
            return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        }

        // Responder con el documento encontrado
        return response()->json($docAlmacen, 200);
    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/docalmacen/{id}",
     *     tags={"Doc Almacen"},
     *     summary="Update an existing Doc Almacen",
     *     description="Update an existing document in the Doc Almacen system",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the document to update",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date_moviment", "quantity", "user_id", "concept_mov_id", "product_id"},
     *             @OA\Property(property="date_moviment", type="string", format="date-time", example="2024-05-22 14:30:00"),
     *             @OA\Property(property="quantity", type="number", format="float", example="1500.75"),
     *             @OA\Property(property="comment", type="string", example="Pago de factura para el producto X"),
     *             @OA\Property(property="user_id", type="integer", example="4"),
     *             @OA\Property(property="concept_mov_id", type="integer", example="2"),
     *             @OA\Property(property="product_id", type="integer", example="5")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/DocAlmacen")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function update(Request $request, $id)
    {
        // Buscar el documento de almacén por ID
        $docAlmacen = DocAlmacen::find($id);

        // Si el documento no se encuentra, responder con un error 404
        if (!$docAlmacen) {
            return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        }

        // Validación de los datos entrantes
        $validatedData = $request->validate([
            'date_moviment' => 'required|date_format:Y-m-d H:i:s', // Validar fecha y hora
            'quantity' => 'required|numeric|gt:0', // Asegura que la cantidad sea mayor a 0
            'comment' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id', // Verifica si el usuario existe
            'concept_mov_id' => 'required|exists:concept_movements,id', // Verifica si el concepto de movimiento existe
            'product_id' => 'required|exists:products,id', // Verifica si el producto existe
        ]);

        // Preparar los datos para la actualización
        $data = [
            'date_moviment' => $validatedData['date_moviment'],
            'quantity' => $validatedData['quantity'],
            'comment' => $validatedData['comment'] ?? null, // Asigna null si no se proporciona comentario
            'user_id' => $validatedData['user_id'],
            'concept_mov_id' => $validatedData['concept_mov_id'],
            'product_id' => $validatedData['product_id'],
        ];

        // Actualizar el documento de almacén con los nuevos datos
        $docAlmacen->update($data);

        // Recuperar el documento actualizado con relaciones
        $docAlmacen = DocAlmacen::with(['user.worker.person', 'user.typeUser', 'concept_mov', 'product.category', 'product.unit', 'product.brand'])
            ->find($docAlmacen->id);

        // Responder con el documento actualizado
        return response()->json($docAlmacen, 200);
    }

/**
 * @OA\Delete(
 *     path="/tecnimotors-backend/public/api/docalmacen/{id}",
 *     tags={"Doc Almacen"},
 *     summary="Delete a Doc Almacen",
 *     description="Delete a document from the Doc Almacen system by its ID",
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the document to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(response="200", description="Document successfully deleted"),
 *     @OA\Response(response="401", description="Unauthenticated"),
 *     @OA\Response(response="404", description="Doc Almacen not found")
 * )
 */
    public function destroy($id)
    {
        // Buscar el documento de almacén por ID
        $docAlmacen = DocAlmacen::find($id);

        // Si el documento no se encuentra, responder con un error 404
        if (!$docAlmacen) {
            return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        }

        // Eliminar el documento de almacén
        $docAlmacen->delete();

        // Responder con éxito
        return response()->json(['message' => 'Documento de almacén eliminado con éxito'], 200);
    }

}
