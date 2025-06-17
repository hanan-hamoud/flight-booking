<?php

namespace Hanan\FlightReservation\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class BookedSeat extends Pivot
{
    use HasFactory;
    protected $table = 'booked_seats';

    protected $fillable = [
        'booking_id',
        'seat_id',
        'price_at_booking',
    ];

    public $incrementing = false;

    protected $primaryKey = ['booking_id', 'seat_id'];

}
