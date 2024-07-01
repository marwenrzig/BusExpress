<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedVehicle extends Model
{
    protected $table = "bus_attribue";
    protected $guarded = ['id'];

    public function trip(){
        return $this->belongsTo(Trip::class);
    }

    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }
}
