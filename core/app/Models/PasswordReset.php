<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = "reinitialisation_mot_de_passe";
    protected $guarded = ['id'];
    public $timestamps = false;
}
