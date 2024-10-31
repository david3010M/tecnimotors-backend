<?php

namespace App\Models\Scopes;

use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UpdateStatusScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->getModel()->newQueryWithoutScopes()->each(function ($commitment) {
            if ($commitment->balance == 0) {
                $commitment->status = Constants::COMMITMENT_PAGADO;
            } else if ($commitment->payment_date < Carbon::today()) {
                $commitment->status = 'VENCIDO';
            } else {
                $commitment->status = 'PENDIENTE';
            }
            $commitment->save();
        });
    }
}
