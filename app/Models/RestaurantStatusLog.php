<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantStatusLog extends Model
{
    protected $fillable = [
        'restaurant_id',
        'previous_is_open',
        'new_is_open',
        'reason',
        'changed_by',
    ];

    protected function casts(): array
    {
        return [
            'previous_is_open' => 'boolean',
            'new_is_open' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
