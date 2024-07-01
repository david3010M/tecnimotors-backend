<?php

namespace App\Http\Controllers;

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

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
