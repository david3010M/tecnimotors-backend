<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;

class PersonController extends Controller
{
   
    public function index()
    {
        return response()->json(Person::simplePaginate(15));
    }

}
