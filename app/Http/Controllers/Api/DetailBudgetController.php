<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DetailBudgetRequest\IndexDetailBudgetRequest;
use App\Http\Requests\DetailBudgetRequest\StoreDetailBudgetRequest;
use App\Http\Requests\DetailBudgetRequest\UpdateDetailBudgetRequest;
use App\Http\Resources\DetailBudgetResource;
use App\Models\DetailBudget;
use App\Services\DetailBudgetService;

class DetailBudgetController extends Controller
{
    protected $service;

    public function __construct(DetailBudgetService $service)
    {
        $this->service = $service;
    }

    public function list(IndexDetailBudgetRequest $request)
    {
        return $this->getFilteredResults(
            DetailBudget::class,
            $request,
            DetailBudget::filters,
            DetailBudget::sorts,
            DetailBudgetResource::class
        );
    }

    public function show($id)
    {
        $item = $this->service->getDetailBudgetById($id);
        if (!$item) return response()->json(['error' => 'Detalle del Presupuesto no encontrado'], 404);
        return new DetailBudgetResource($item);
    }

    public function store(StoreDetailBudgetRequest $request)
    {
        $item = $this->service->createDetailBudget($request->validated());
        return new DetailBudgetResource($item);
    }

    public function update(UpdateDetailBudgetRequest $request, $id)
    {
        $item = $this->service->getDetailBudgetById($id);
        if (!$item) return response()->json(['error' => 'Detalle del Presupuesto no encontrado'], 404);
        $item = $this->service->updateDetailBudget($item, $request->validated());
        return new DetailBudgetResource($item);
    }

    public function destroy($id)
    {
        $item = $this->service->getDetailBudgetById($id);
        if (!$item) return response()->json(['error' => 'Detalle del Presupuesto no encontrado'], 404);
        $this->service->destroyById($id);
        return response()->json(['message' => 'DetailBudget eliminado'], 200);
    }
}