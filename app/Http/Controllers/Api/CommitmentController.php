<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commitment;
use Illuminate\Http\Request;

class CommitmentController extends Controller
{
    public function index()
    {
        return Commitment::all();
    }

    public function store(Request $request)
    {

    }

    public function show(int $id)
    {

    }

    public function update(Request $request, int $id)
    {

    }

    public function destroy(int $id)
    {

    }
}
