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
            'product_id' => 'nullable|exists:products,id', // Verifica si el producto existe
        ];
    }
}
