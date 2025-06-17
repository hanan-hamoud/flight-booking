<?php

namespace Hanan\FlightReservation\Database\Seeders;

use Illuminate\Database\Seeder;
use Hanan\FlightReservation\Models\Airport;
use Hanan\FlightReservation\Models\Flight;
use Hanan\FlightReservation\Models\Seat;
use Hanan\FlightReservation\Models\Booking;
use Hanan\FlightReservation\Models\Payment;
use Hanan\FlightReservation\Models\FareRule;

class FlightReservationSeeder extends Seeder
{
    public function run()
    {
        $airports = Airport::factory(5)->create();

        $flights = Flight::factory(10)
            ->create()
            ->each(function ($flight) use ($airports) {
                $flight->departure_airport_id = $airports->random()->id;
                $arrivalAirport = $airports->where('id', '!=', $flight->departure_airport_id)->random();
                $flight->arrival_airport_id = $arrivalAirport->id;
                $flight->save();

                Seat::factory(30)->create([
                    'flight_id' => $flight->id,
                ]);
            });
        FareRule::factory(5)->create();

        $bookings = Booking::factory(15)
            ->create()
            ->each(function ($booking) use ($flights) {
                $booking->flight_id = $flights->random()->id;
                $booking->save();

                Payment::factory()->create([
                    'booking_id' => $booking->id,
                ]);
            });

    }
}
