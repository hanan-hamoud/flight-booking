<?php

namespace Hanan\FlightReservation\Database\Factories;

use Hanan\FlightReservation\Models\Booking;
use Hanan\FlightReservation\Models\User; // Assuming you have User model
use Hanan\FlightReservation\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            'booking_reference' => strtoupper($this->faker->unique()->bothify('BR######')),
            'user_id' => User::factory(),
            'flight_id' => Flight::factory(),
            'status' => $this->faker->randomElement(['pending_payment', 'confirmed', 'cancelled']),
            'total_price' => $this->faker->randomFloat(2, 100, 1000),
            'booking_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
