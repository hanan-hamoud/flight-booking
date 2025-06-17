<?php

namespace Hanan\FlightReservation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Passenger extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'passport_number',
        'nationality',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
    

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
