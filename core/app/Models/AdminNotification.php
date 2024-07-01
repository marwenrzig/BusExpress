<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $table = "notification_admin";
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
