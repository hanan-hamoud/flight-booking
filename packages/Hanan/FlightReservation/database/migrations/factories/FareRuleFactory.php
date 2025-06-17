<?php

namespace Hanan\FlightReservation\Database\Factories;

use Hanan\FlightReservation\Models\FareRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class FareRuleFactory extends Factory
{
    protected $model = FareRule::class;

    public function definition()
    {
        return [
            'rule_name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'applies_to_class' => $this->faker->randomElement(['economy', 'business']),
            'cancellation_deadline_hours' => $this->faker->numberBetween(0, 72),
        ];
    }
}
