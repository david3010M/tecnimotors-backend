<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestDocAlmacen;
use App\Http\Requests\StoreRequestDocAlmacen;
use App\Http\Requests\UpdateRequestDocAlmacen;
use App\Http\Resources\DocAlmacenResource;
use App\Models\ConceptMov;
use App\Models\DocAlmacen;
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
    public function store(StoreRequestDocAlmacen $request)
    {
        $docAlmacen = DocAlmacen::create($request->validated());
        $docAlmacen = DocAlmacen::find($docAlmacen->id);
        $concepto = ConceptMov::find($docAlmacen->concept_mov_id);

        $docAlmacen->typemov=$concepto->typemov;
        $docAlmacen->concept=$concepto->concept;
        $docAlmacen->save();

        $product = Product::find($docAlmacen->product_id);
        
        if ($concepto->typemov === 'INGRESO') {
            // Si es un ingreso, sumamos al stock
            $product->stock += $docAlmacen->quantity;
        } elseif ($concepto->typemov === 'EGRESO') {
            // Si es un egreso, restamos del stock
            $product->stock -= $docAlmacen->quantity;
        }
        $product->save();
        
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
        if (!$docAlmacen) return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        return response()->json(DocAlmacenResource::make($docAlmacen));
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
    public function update(UpdateRequestDocAlmacen $request, $id)
    {
        $docAlmacen = DocAlmacen::find($id);
        if (!$docAlmacen) {
            return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        }
    
        $product = Product::find($docAlmacen->product_id);
        if (!$product) {
            return response()->json(['message' => 'Producto asociado no encontrado'], 404);
        }
    
        $conceptOriginal = ConceptMov::find($docAlmacen->concept_mov_id);
        if (!$conceptOriginal) {
            return response()->json(['message' => 'Concepto de movimiento original no encontrado'], 404);
        }

        $docAlmacen->typemov=$conceptOriginal->typemov;
        $docAlmacen->concept=$conceptOriginal->concept;
        $docAlmacen->save();
    
        // Revertir stock según el movimiento original
        $product->stock += ($conceptOriginal->typemov === 'INGRESO' ? -$docAlmacen->quantity : $docAlmacen->quantity);
    
        // Actualizar el documento
        $docAlmacen->update($request->validated());
    
        $conceptUpdated = ConceptMov::find($docAlmacen->concept_mov_id);
        if (!$conceptUpdated) {
            return response()->json(['message' => 'Concepto de movimiento actualizado no encontrado'], 404);
        }
    
        // Aplicar stock según el movimiento actualizado
        $product->stock += ($conceptUpdated->typemov === 'INGRESO' ? $docAlmacen->quantity : -$docAlmacen->quantity);
    
        $product->save();
    
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
        if (!$docAlmacen) return response()->json(['message' => 'Documento de almacén no encontrado'], 404);
        $docAlmacen->delete();
        return response()->json(['message' => 'Documento de almacén eliminado con éxito'], 200);
    }

}
