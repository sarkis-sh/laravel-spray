<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use App\Traits\ResourceTrait;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    use ResponseTrait,
        ResourceTrait;
}
