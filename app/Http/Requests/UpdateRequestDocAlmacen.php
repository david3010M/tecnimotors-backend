<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     title="UpdateRequestDocAlmacen",
 *     description="Update Request Doc Almacen",
 *     type="object",
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
class UpdateRequestDocAlmacen extends UpdateRequest
{
    public function rules()
    {
        return [
            'date_moviment' => 'nullable', // Validar fecha y hora
            'quantity' => 'nullable|numeric|gt:0', // Asegura que la cantidad sea mayor a 0
            'comment' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id', // Verifica si el usuario existe
            'person_id' => 'nullable|exists:people,id', // Verifica si la persona existe
            'concept_mov_id' => 'nullable|exists:concept_movs,id', // Verifica si el concepto de movimiento existe

            'products' => 'required|array|min:1', // Debe ser un array con al menos un elemento
            'products.*.product_id' => 'required|exists:products,id', // Cada elemento debe tener un product_id válido
            'products.*.quantity' => 'required|numeric|gt:0', // Cada producto debe tener una cantidad válida
            'products.*.comment' => 'nullable|string|max:255', // comentario del producto
        ];
    }
}
