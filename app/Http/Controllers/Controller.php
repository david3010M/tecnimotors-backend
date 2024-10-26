<?php

namespace App\Http\Controllers;

use App\Traits\Filterable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *             title="API's Tecni Motors",
 *             version="1.0",
 *             description="API's for TecniMotors",
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="Authorization",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *  )
 *
 * @OA\Schema (
 *      schema="ValidationError",
 *      @OA\Property(property="error", type="string", example="The pagination must be an integer.")
 *  )
 *
 * @OA\Schema (
 *      schema="Unauthenticated",
 *      @OA\Property(property="error", type="string", example="Unauthenticated.")
 *  )
 */
class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Filterable;

    public function nextCorrelative($model, $field, $length = 8)
    {
        $last = $model::orderBy($field, 'desc')->first();
        $correlative = $last ? $last->$field + 1 : 1;
        return str_pad($correlative, $length, '0', STR_PAD_LEFT);
    }

    public function nextCorrelativeQuery($query, $field, $length = 8)
    {
        $last = $query->orderBy($field, 'desc')->first();
        $correlative = $last ? $last->$field + 1 : 1;
        return str_pad($correlative, $length, '0', STR_PAD_LEFT);
    }
}
