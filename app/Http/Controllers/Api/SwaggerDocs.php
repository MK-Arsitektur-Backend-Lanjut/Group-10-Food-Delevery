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
#[OA\Schema(
    schema: "StoreRestaurantRequest",
    type: "object",
    required: ["name", "address"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "Warung Nusantara Baru"),
        new OA\Property(property: "description", type: "string", example: "Deskripsi restoran", nullable: true),
        new OA\Property(property: "address", type: "string", example: "Jalan Merdeka No. 1"),
        new OA\Property(property: "phone", type: "string", example: "08123456789", nullable: true),
        new OA\Property(property: "status", type: "string", example: "active", nullable: true),
        new OA\Property(property: "is_open", type: "boolean", example: true, nullable: true),
        new OA\Property(property: "open_time", type: "string", format: "time", example: "08:00", nullable: true),
        new OA\Property(property: "close_time", type: "string", format: "time", example: "22:00", nullable: true)
    ]
)]
#[OA\Schema(
    schema: "UpdateRestaurantRequest",
    type: "object",
    properties: [
        new OA\Property(property: "name", type: "string", example: "Warung Nusantara Updated"),
        new OA\Property(property: "description", type: "string", example: "Deskripsi restoran", nullable: true),
        new OA\Property(property: "address", type: "string", example: "Jalan Merdeka No. 1"),
        new OA\Property(property: "phone", type: "string", example: "08123456789", nullable: true),
        new OA\Property(property: "status", type: "string", example: "active", nullable: true),
        new OA\Property(property: "is_open", type: "boolean", example: true, nullable: true),
        new OA\Property(property: "open_time", type: "string", format: "time", example: "08:00", nullable: true),
        new OA\Property(property: "close_time", type: "string", format: "time", example: "22:00", nullable: true)
    ]
)]
#[OA\Schema(
    schema: "UpdateRestaurantOperationalStatusRequest",
    type: "object",
    required: ["is_open"],
    properties: [
        new OA\Property(property: "is_open", type: "boolean", example: true),
        new OA\Property(property: "reason", type: "string", example: "Jam operasional", nullable: true),
        new OA\Property(property: "changed_by", type: "string", example: "admin", nullable: true)
    ]
)]
#[OA\Schema(
    schema: "StoreMenuCategoryRequest",
    type: "object",
    required: ["name"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "Minuman Dingin"),
        new OA\Property(property: "sort_order", type: "integer", example: 1, nullable: true),
        new OA\Property(property: "is_active", type: "boolean", example: true, nullable: true)
    ]
)]
#[OA\Schema(
    schema: "UpdateMenuCategoryRequest",
    type: "object",
    properties: [
        new OA\Property(property: "name", type: "string", example: "Minuman Dingin updated"),
        new OA\Property(property: "sort_order", type: "integer", example: 1, nullable: true),
        new OA\Property(property: "is_active", type: "boolean", example: true, nullable: true)
    ]
)]
#[OA\Schema(
    schema: "StoreMenuItemRequest",
    type: "object",
    required: ["name", "price"],
    properties: [
        new OA\Property(property: "menu_category_id", type: "integer", example: 1, nullable: true),
        new OA\Property(property: "name", type: "string", example: "Es Teh Manis"),
        new OA\Property(property: "description", type: "string", example: "Es teh manis segar", nullable: true),
        new OA\Property(property: "price", type: "number", format: "float", example: 5000),
        new OA\Property(property: "is_active", type: "boolean", example: true, nullable: true),
        new OA\Property(property: "is_available", type: "boolean", example: true, nullable: true),
        new OA\Property(property: "prep_time_minutes", type: "integer", example: 5, nullable: true),
        new OA\Property(property: "image_url", type: "string", example: "http://example.com/image.jpg", nullable: true)
    ]
)]
#[OA\Schema(
    schema: "UpdateMenuItemRequest",
    type: "object",
    properties: [
        new OA\Property(property: "menu_category_id", type: "integer", example: 1, nullable: true),
        new OA\Property(property: "name", type: "string", example: "Es Teh Manis Updated"),
        new OA\Property(property: "description", type: "string", example: "Es teh manis segar", nullable: true),
        new OA\Property(property: "price", type: "number", format: "float", example: 5000),
        new OA\Property(property: "is_active", type: "boolean", example: true, nullable: true),
        new OA\Property(property: "is_available", type: "boolean", example: true, nullable: true),
        new OA\Property(property: "prep_time_minutes", type: "integer", example: 5, nullable: true),
        new OA\Property(property: "image_url", type: "string", example: "http://example.com/image.jpg", nullable: true)
    ]
)]
#[OA\Schema(
    schema: "UpdateMenuAvailabilityRequest",
    type: "object",
    required: ["is_available"],
    properties: [
        new OA\Property(property: "is_available", type: "boolean", example: false)
    ]
)]
class SwaggerDocs
{
    // Class just for Swagger Schema Definitions
}
