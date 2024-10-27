<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequestSaleDetail;
use App\Http\Resources\SaleDetailResource;
use App\Models\SaleDetail;
use App\Http\Requests\StoreSaleDetailRequest;
use App\Http\Requests\UpdateSaleDetailRequest;

class SaleDetailController extends Controller
{
    public function index(IndexRequestSaleDetail $request)
    {
        return $this->getFilteredResults(
            SaleDetail::class,
            $request,
            SaleDetail::filters,
            SaleDetail::sorts,
            SaleDetailResource::class
        );
    }

    public function store(StoreSaleDetailRequest $request)
    {

    }

    public function show(int $id)
    {
        //
    }

    public function update(UpdateSaleDetailRequest $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {

    }
}
