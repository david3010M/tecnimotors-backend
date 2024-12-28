<?php

namespace App\Http\Requests;

class UpdateRequestDocAlmacen extends UpdateRequest
{
    public function rules()
    {
        return [
            'date_moviment' => 'nullable', // Validar fecha y hora
            'quantity' => 'nullable|numeric|gt:0', // Asegura que la cantidad sea mayor a 0
            'comment' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id', // Verifica si el usuario existe
            'concept_mov_id' => 'nullable|exists:concept_movs,id', // Verifica si el concepto de movimiento existe
            // 'product_id' => 'nullable|exists:products,id', // Verifica si el producto existe

            'products' => 'required|array|min:1', // Debe ser un array con al menos un elemento
            'products.*.product_id' => 'required|exists:products,id', // Cada elemento debe tener un product_id válido
            'products.*.quantity' => 'required|numeric|gt:0', // Cada producto debe tener una cantidad válida
            'products.*.comment' => 'required|string|max:255', // comentario del producto
        ];
    }
}
