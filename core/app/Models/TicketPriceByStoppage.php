<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPriceByStoppage extends Model
{
    protected $table = 'prix_ticket_par_arret';
    protected $guarded = ['id'];
    protected $casts = [
        'source_destination' => 'array'
    ];
}
