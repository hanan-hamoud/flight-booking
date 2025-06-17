<?php

namespace Hanan\FlightReservation\Database\Factories;

use Hanan\FlightReservation\Models\Payment;
use Hanan\FlightReservation\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'booking_id' => Booking::factory(),
            'transaction_id' => strtoupper($this->faker->bothify('TXN######')),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'currency' => $this->faker->currencyCode(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'paid_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
