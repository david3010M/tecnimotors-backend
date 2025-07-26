<?php

namespace App\Services;

use App\Models\DetailBudget;

class DetailBudgetService
{
    public function getDetailBudgetById(int $id): ?DetailBudget
    {
        return DetailBudget::find($id);
    }

    public function createDetailBudget(array $data): DetailBudget
    {
        $data['status'] = 'Pendiente'; // Default status
        return DetailBudget::create($data);
    }

    public function updateDetailBudget(DetailBudget $instance, array $data): DetailBudget
    {
        $filteredData = array_intersect_key($data, $instance->getAttributes());
        $instance->update($filteredData);
        return $instance;
    }

    public function destroyById($id)
    {
        return DetailBudget::find($id)?->delete() ?? false;
    }
}