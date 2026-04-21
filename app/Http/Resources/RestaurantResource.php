<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => $this->status?->value ?? $this->status,
            'is_open' => (bool) $this->is_open,
            'open_time' => $this->open_time ? $this->open_time->format('H:i') : null,
            'close_time' => $this->close_time ? $this->close_time->format('H:i') : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
