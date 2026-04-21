<?php

namespace App\Models;

use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'menu_category_id',
        'name',
        'description',
        'price',
        'is_active',
        'is_available',
        'prep_time_minutes',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'prep_time_minutes' => 'integer',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class);
    }
}
