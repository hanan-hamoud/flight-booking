<?php

namespace Hanan\FlightReservation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'paid_at',
    ];

    protected $dates = ['paid_at'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
