<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', description: 'Food Delivery API Documentation', title: 'Food Delivery API')]
#[OA\Server(url: '/api/v1', description: 'API Server')]
abstract class Controller extends BaseController
{
    //
}
