<?php

namespace App\Http\Requests;

class StoreRequestDocAlmacen extends StoreRequest
{
    public function rules()
    {
        return [
            'date_moviment' => 'required', // Validar fecha y hora
            'quantity' => 'required|numeric|gt:0', // Asegura que la cantidad sea mayor a 0
            'comment' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id', // Verifica si el usuario existe
            'concept_mov_id' => 'required|exists:concept_movs,id', // Verifica si el concepto de movimiento existe

            'products' => 'required|array|min:1', // Valida que sea un array con al menos un elemento
            'products.*.product_id' => 'required|exists:products,id', // id del producto
            'products.*.quantity' => 'required|integer|min:1', // cantidad del producto
            'products.*.comment' => 'nullable|string|max:255', // comentario del producto
        ];
    }
}
