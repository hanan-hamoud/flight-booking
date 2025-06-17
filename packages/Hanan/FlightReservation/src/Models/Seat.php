<?php

namespace Hanan\FlightReservation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Seat extends Model
{
    use HasFactory;
    protected $fillable = ['flight_id', 'seat_number', 'seat_class', 'is_available'];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booked_seats')
            ->withPivot('price_at_booking');
    }
}
