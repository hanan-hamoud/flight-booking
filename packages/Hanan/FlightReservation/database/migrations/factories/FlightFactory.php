<?php

namespace Hanan\FlightReservation\Database\Factories;

use Hanan\FlightReservation\Models\Flight;
use Hanan\FlightReservation\Models\Airport;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlightFactory extends Factory
{
    protected $model = Flight::class;

    public function definition()
    {
        return [
            'flight_number' => strtoupper($this->faker->bothify('FL###')),
            'departure_airport_id' => Airport::factory(),
            'arrival_airport_id' => Airport::factory(),
            'departure_time' => $this->faker->dateTimeBetween('+1 days', '+2 days'),
            'arrival_time' => $this->faker->dateTimeBetween('+3 days', '+4 days'),
            'total_economy_seats' => $this->faker->numberBetween(50, 200),
            'total_business_seats' => $this->faker->numberBetween(10, 50),
            'base_economy_price' => $this->faker->randomFloat(2, 100, 500),
            'base_business_price' => $this->faker->randomFloat(2, 500, 1000),
            'status' => $this->faker->randomElement(['scheduled', 'cancelled', 'delayed']),
        ];
    }
}
