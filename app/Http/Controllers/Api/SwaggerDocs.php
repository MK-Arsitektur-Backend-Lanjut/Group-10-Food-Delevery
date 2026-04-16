<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Restaurant",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Warung Nusantara"),
        new OA\Property(property: "status", type: "string", example: "active"),
        new OA\Property(property: "is_open", type: "boolean", example: true)
    ]
)]
#[OA\Schema(
    schema: "MenuCategory",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "restaurant_id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Makanan Utama"),
        new OA\Property(property: "sort_order", type: "integer", example: 0),
        new OA\Property(property: "is_active", type: "boolean", example: true)
    ]
)]
#[OA\Schema(
    schema: "MenuItem",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "restaurant_id", type: "integer", example: 1),
        new OA\Property(property: "menu_category_id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Nasi Goreng"),
        new OA\Property(property: "price", type: "number", format: "float", example: 25000),
        new OA\Property(property: "is_active", type: "boolean", example: true),
        new OA\Property(property: "is_available", type: "boolean", example: true)
    ]
)]
#[OA\Schema(
    schema: "ValidateOrderItemsRequest",
    type: "object",
    required: ["restaurant_id", "items"],
    properties: [
        new OA\Property(property: "restaurant_id", type: "integer", example: 1),
        new OA\Property(
            property: "items",
            type: "array",
            items: new OA\Items(
                type: "object",
                required: ["menu_item_id", "qty"],
                properties: [
                    new OA\Property(property: "menu_item_id", type: "integer", example: 101),
                    new OA\Property(property: "qty", type: "integer", example: 2)
                ]
            )
        )
    ]
)]
class SwaggerDocs
{
    // Class just for Swagger Schema Definitions
}
