<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'menu_category_id' => $this->menu_category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'is_active' => (bool) $this->is_active,
            'is_available' => (bool) $this->is_available,
            'prep_time_minutes' => $this->prep_time_minutes,
            'image_url' => $this->image_url,
            'category' => new MenuCategoryResource($this->whenLoaded('menuCategory')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
