<?php

namespace Hanan\FlightReservation\Database\Factories;

use Hanan\FlightReservation\Models\BookedSeat;
use Hanan\FlightReservation\Models\Booking;
use Hanan\FlightReservation\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookedSeatFactory extends Factory
{
    protected $model = BookedSeat::class;

    public function definition()
    {
        return [
            'booking_id' => Booking::factory(),
            'seat_id' => Seat::factory(),
            'price_at_booking' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
