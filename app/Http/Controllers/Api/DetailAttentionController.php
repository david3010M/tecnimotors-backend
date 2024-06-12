<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\DetailAttention;
use Illuminate\Http\Request;

class DetailAttentionController extends Controller
{
    public function show(int $id)
    {
        $detailAttention = DetailAttention::find($id);
        $attention = Attention::with('details')->find($detailAttention->attention_id);
        $products = [];

        foreach ($attention->details as $detail) {
            if ($detail->product_id != null)
                $products[] = $detail->product;
        }

        return response()->json(
            [
                'detailAttention' => $detailAttention,
                'observation' => $attention->observations,
                'product' => $products,
            ]
        );
    }

    public function getDetailAttentionByWorkerId(int $id)
    {
        $detailAttention = DetailAttention::where('worker_id', $id)->get();
        return response()->json($detailAttention);
    }

}
