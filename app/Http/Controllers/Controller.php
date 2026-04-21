<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Order API",
 *     version="1.0.0",
 *     description="API untuk manajemen order"
 * )
 */
class Controller extends BaseController
use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", description: "Food Delivery API Documentation", title: "Food Delivery API")]
#[OA\Server(url: "/api/v1", description: "API Server")]
abstract class Controller
{
    //
}