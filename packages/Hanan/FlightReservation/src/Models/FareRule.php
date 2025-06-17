<?php

namespace Hanan\FlightReservation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class FareRule extends Model
{

    use HasFactory;
    protected $fillable = [
        'rule_name',
        'description',
        'applies_to_class',
        'cancellation_deadline_hours',
    ];
}
