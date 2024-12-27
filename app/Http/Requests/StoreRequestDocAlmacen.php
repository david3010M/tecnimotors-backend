<?php

namespace App\Http\Requests;

class StoreRequestDocAlmacen extends StoreRequest
{
    public function rules()
    {
        return [
            'date_moviment' => 'required|date_format:Y-m-d H:i:s', // Validar fecha y hora
            'quantity' => 'required|numeric|gt:0', // Asegura que la cantidad sea mayor a 0
            'comment' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id', // Verifica si el usuario existe
            'concept_mov_id' => 'required|exists:concept_movs,id', // Verifica si el concepto de movimiento existe
            'product_id' => 'required|exists:products,id', // Verifica si el producto existe
        ];
    }
}
