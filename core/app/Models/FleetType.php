<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetType extends Model
{
    protected $guarded = ['id'];
    protected $table = 'type_bus';
    protected $casts = [
        'places_pont' => 'object'
    ];

    public function vehicles(){
        return $this->hasMany(Vehicle::class);
    }

    public function activeVehicles(){
        return $this->hasMany(Vehicle::class)->where('status', 1);
    }

    //scope active
    public function scopeActive(){
        return $this->where('status', 1);
    }
}
