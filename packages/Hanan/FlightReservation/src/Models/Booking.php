<?php

namespace Hanan\FlightReservation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_reference',
        'user_id',
        'flight_id',
        'status',
        'total_price',
        'booking_date',
    ];

    protected $dates = ['booking_date'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
    }

    public function seats(): BelongsToMany
    {
        return $this->belongsToMany(Seat::class, 'booked_seats')
            ->withPivot('price_at_booking');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function ticket()
{
    return $this->hasOne(Ticket::class);
}

}
