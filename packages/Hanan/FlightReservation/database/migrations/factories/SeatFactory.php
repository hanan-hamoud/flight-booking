<?php

namespace Hanan\FlightReservation\Database\Factories;

use Hanan\FlightReservation\Models\Seat;
use Hanan\FlightReservation\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatFactory extends Factory
{
    protected $model = Seat::class;

    public function definition()
    {
        return [
            'flight_id' => Flight::factory(),
            'seat_number' => $this->faker->bothify('??###'),
            'seat_class' => $this->faker->randomElement(['economy', 'business']),
            'is_available' => $this->faker->boolean(90),
        ];
    }
}
