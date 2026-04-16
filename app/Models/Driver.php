<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'vehicle', 'status'];

    protected $hidden = ['password'];

    public function deliveryHistories()
    {
        return $this->hasMany(DeliveryHistory::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
