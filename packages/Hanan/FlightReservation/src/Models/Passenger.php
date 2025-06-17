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

    protected $dates = ['date_of_birth'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
