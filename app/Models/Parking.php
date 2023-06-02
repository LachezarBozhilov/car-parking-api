<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_number',
        'vehicle_category',
        'entry_time',
        'exit_time',
        'vehicle_card'
    ];
}
