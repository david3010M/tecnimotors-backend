<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     title="StoreRequestDocAlmacen",
 *     description="Store Request Doc Almacen",
 *     type="object",
 *     required={"date_moviment", "quantity", "person_id", "user_id", "concept_mov_id", "products"},
 *     @OA\Property(property="date_moviment", type="string", format="date-time", example="2021-09-30 12:00:00"),
 *     @OA\Property(property="quantity", type="integer", example=1),
 *     @OA\Property(property="comment", type="string", example="Comentario"),
 *     @OA\Property(property="person_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="concept_mov_id", type="integer", example=1),
 *     @OA\Property(property="products", type="array", @OA\Items(
 *         @OA\Property(property="product_id", type="integer", example=1),
 *         @OA\Property(property="quantity", type="integer", example=1),
 *         @OA\Property(property="comment", type="string", example="Comentario")
 *     ))
 * )
 */
class StoreRequestDocAlmacen extends StoreRequest
{
    public function rules()
    {
        return [
            'date_moviment' => 'required', // Validar fecha y hora
            'quantity' => 'required|numeric|gt:0', // Asegura que la cantidad sea mayor a 0
            'comment' => 'nullable|string|max:255',
            'person_id' => 'required|exists:people,id', // Verifica si la persona existe
            'user_id' => 'required|exists:users,id', // Verifica si el usuario existe
            'concept_mov_id' => 'required|exists:concept_movs,id', // Verifica si el concepto de movimiento existe

            'products' => 'required|array|min:1', // Valida que sea un array con al menos un elemento
            'products.*.product_id' => 'required|exists:products,id', // id del producto
            'products.*.quantity' => 'required|integer|min:1', // cantidad del producto
            'products.*.comment' => 'nullable|string|max:255', // comentario del producto
        ];
    }
}
