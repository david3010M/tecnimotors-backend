<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestDocAlmacen;
use App\Http\Requests\StoreRequestDocAlmacen;
use App\Http\Requests\UpdateRequestDocAlmacen;
use App\Http\Resources\DocAlmacenResource;
use App\Models\ConceptMov;
use App\Models\DocAlmacen;
use App\Models\Docalmacen_details;
use App\Models\Product;

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

    public function index(IndexRequestDocAlmacen $request)
    {
        return $this->getFilteredResults(
            DocAlmacen::class,
            $request,
            DocAlmacen::filters,
            DocAlmacen::sorts,
            DocAlmacenResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/tecnimotors-backend/public/api/docalmacen",
     *     tags={"Doc Almacen"},
     *     summary="Create a new Doc Almacen",
     *     description="Store a new document in the Doc Almacen system with multiple products.",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date_moviment", "user_id", "concept_mov_id", "products"},
     *             @OA\Property(property="date_moviment", type="string", format="date-time", example="2024-05-22 14:30:00"),
     *             @OA\Property(property="comment", type="string", example="Pago de factura para varios productos"),
     *             @OA\Property(property="user_id", type="integer", example="4"),
     *             @OA\Property(property="concept_mov_id", type="integer", example="2"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 description="List of products associated with the Doc Almacen",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer", example="5", description="The ID of the product"),
     *                     @OA\Property(property="quantity", type="number", format="integer", example="100", description="Quantity of the product"),
     *         @OA\Property(property="commnet", type="string", example="Comment", description="Comment of the product involved in the movement")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/DocAlmacen")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */

    public function store(StoreRequestDocAlmacen $request)
    {
        // Crear el registro principal en la tabla DocAlmacen
        $docAlmacen = DocAlmacen::create($request->validated());

        $lastCorrelativo = DocAlmacen::max('id') ? DocAlmacen::orderBy('id', 'desc')->value('sequentialnumber') : 'MA01-00000000';
        $nextCorrelativo = 'MA01-' . str_pad((int)substr($lastCorrelativo, -8) + 1, 8, '0', STR_PAD_LEFT);
        $docAlmacen->sequentialnumber = $nextCorrelativo;
        $docAlmacen->save();

        // Obtener el concepto de movimiento asociado
        $concepto = ConceptMov::find($docAlmacen->concept_mov_id);

        // Actualizar campos adicionales en DocAlmacen
        $docAlmacen->typemov = $concepto->typemov;
        $docAlmacen->concept = $concepto->concept;
        $docAlmacen->save();

        // Iterar sobre el arreglo de productos para crear detalles
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);

            if (!$product) {
                continue; // Saltar si el producto no existe
            }

            // Verificar si el producto ya existe en los detalles
            $existingDetail = $docAlmacen->details()->where('product_id', $productData['product_id'])->first();

            if ($existingDetail) {
                // Si el producto ya está en los detalles, sumar la cantidad
                $existingDetail = Docalmacen_details::find($existingDetail->id);
                $existingDetail->quantity += $productData['quantity'];
                $existingDetail->comment = $existingDetail->comment . ' ' . ($productData['comment'] ?? '');

                $existingDetail->save();

                // Actualizar el stock basado en el tipo de movimiento
                $quantityDifference = $productData['quantity']; // La cantidad que se va a agregar
                $product->stock += ($concepto->typemov === 'INGRESO' ? $quantityDifference : -$quantityDifference);
            } else {
                // Si el producto no existe, agregar un nuevo detalle
                $lastDetailCorrelativo = Docalmacen_details::max('id') ? Docalmacen_details::orderBy('id', 'desc')->value('sequentialnumber') : 'DMA1-00000000';
                $nextDetailCorrelativo = 'DMA1-' . str_pad((int)substr($lastDetailCorrelativo, -8) + 1, 8, '0', STR_PAD_LEFT);


                Docalmacen_details::create([
                    'doc_almacen_id' => $docAlmacen->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'comment' => $productData['comment'] ?? '',
                    'sequentialnumber' => $nextDetailCorrelativo, // Añadir el correlativo con la serie DMA1
                ]);

                // Actualizar el stock según el tipo de movimiento
                $product->stock += ($concepto->typemov === 'INGRESO' ? $productData['quantity'] : -$productData['quantity']);
            }

            // Guardar los cambios en el stock
            $product->save();
        }

        return response()->json(DocAlmacenResource::make($docAlmacen));
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
    public function show(int $id)
    {
        $docAlmacen = DocAlmacen::find($id);
        if (!$docAlmacen) {
            return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        }

        return response()->json(DocAlmacenResource::make($docAlmacen));
    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/docalmacen/{id}",
     *     tags={"Doc Almacen"},
     *     summary="Update an existing Doc Almacen",
     *     description="Update an existing document in the Doc Almacen system, including stock adjustments based on the movement concept",
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
     *             required={"date_moviment", "user_id", "concept_mov_id", "products"},
     *             @OA\Property(property="date_moviment", type="string", format="date-time", example="2024-05-22 14:30:00", description="Date and time of the movement"),
     *             @OA\Property(property="user_id", type="integer", example="4", description="ID of the user making the update"),
     *             @OA\Property(property="concept_mov_id", type="integer", example="2", description="ID of the concept of the movement"),
     *             @OA\Property(property="products", type="array", @OA\Items(
     *                 @OA\Property(property="product_id", type="integer", example="5", description="ID of the product"),
     *                 @OA\Property(property="quantity", type="number", format="integer", example="15", description="Quantity of the product involved in the movement"),
     *   @OA\Property(property="commnet", type="string", example="Comment", description="Comment of the product involved in the movement")
     *             ), description="List of products and quantities involved in the movement"),
     *             @OA\Property(property="comment", type="string", example="Pago de factura para el producto X", description="Optional comment related to the movement")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Document updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DocAlmacen")
     *     ),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function update(UpdateRequestDocAlmacen $request, $id)
    {
        $docAlmacen = DocAlmacen::find($id);
        if (!$docAlmacen) {
            return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        }

        $conceptOriginal = ConceptMov::find($docAlmacen->concept_mov_id);
        if (!$conceptOriginal) {
            return response()->json(['message' => 'Concepto de movimiento original no encontrado'], 404);
        }

        // Revertir cambios de stock según el movimiento original solo para los detalles existentes
        $originalDetails = $docAlmacen->details()->with('product')->get();
        foreach ($originalDetails as $detail) {
            $product = $detail->product;
            if ($product) {
                // Revertir el stock según el tipo de movimiento original
                $product->stock += ($conceptOriginal->typemov === 'INGRESO' ? -$detail->quantity : $detail->quantity);
                $product->save();
            }
        }

        // Actualizar el documento principal con los nuevos datos
        $docAlmacen->update($request->validated());

        $conceptUpdated = ConceptMov::find($docAlmacen->concept_mov_id);
        if (!$conceptUpdated) {
            return response()->json(['message' => 'Concepto de movimiento actualizado no encontrado'], 404);
        }

        // Obtener todos los productos necesarios de la base de datos
        $productIds = collect($request->products)->pluck('product_id')->unique()->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Procesar los nuevos detalles de productos
        foreach ($request->products as $productData) {
            $product = $products->get($productData['product_id']);
            if (!$product) {
                continue; // Saltar si el producto no existe
            }

            // Verificar si el producto ya existe en los detalles
            $existingDetail = $docAlmacen->details()->where('product_id', $productData['product_id'])->first();

            if ($existingDetail) {
                // Si el producto ya está en los detalles, actualizar la cantidad

                $existingDetail = Docalmacen_details::find($existingDetail->id);
                $quantityDifference = $productData['quantity'];
                $existingDetail->quantity += $productData['quantity'];
                $existingDetail->comment = $existingDetail->comment . ' ' . ($productData['comment'] ?? '');
                $existingDetail->save();

                // Actualizar el stock basado en el tipo de movimiento
                $product->stock += ($conceptUpdated->typemov === 'INGRESO' ? $quantityDifference : -$quantityDifference);
            } else {
                // Si el producto no existe, agregar un nuevo detalle

                $lastDetailCorrelativo = Docalmacen_details::max('id') ? Docalmacen_details::orderBy('id', 'desc')->value('sequentialnumber') : 'DMA1-00000000';
                $nextDetailCorrelativo = 'DMA1-' . str_pad((int)substr($lastDetailCorrelativo, -8) + 1, 8, '0', STR_PAD_LEFT);

                Docalmacen_details::create([
                    'doc_almacen_id' => $docAlmacen->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'comment' => $productData['comment'] ?? '',
                    'sequentialnumber' => $nextDetailCorrelativo,
                ]);

                // Actualizar el stock según el tipo de movimiento
                $product->stock += ($conceptUpdated->typemov === 'INGRESO' ? $productData['quantity'] : -$productData['quantity']);
            }

            // Guardar los cambios en el stock
            $product->save();
        }

        return response()->json(DocAlmacenResource::make($docAlmacen));
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
        $docAlmacen = DocAlmacen::find($id);
        if (!$docAlmacen) {
            return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        }

        $concepto = ConceptMov::withTrashed()->find($docAlmacen->concept_mov_id);

        // Revertir cambios de stock para cada detalle
        $details = $docAlmacen->details; // Relación con DocAlmacen_details
        foreach ($details as $detail) {
            $product = Product::find($detail->product_id);
            if ($product) {
                $product->stock += ($concepto->typemov === 'INGRESO' ? -$detail->quantity : $detail->quantity);
                $product->save();
            }
        }

        // Eliminar los detalles
        $docAlmacen->details()->delete();

        // Eliminar el documento principal
        $docAlmacen->delete();

        return response()->json(['message' => 'Documento de almacén y sus detalles eliminados con éxito'], 200);
    }

}
