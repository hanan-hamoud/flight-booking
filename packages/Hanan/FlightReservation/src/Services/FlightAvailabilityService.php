<?php

namespace Hanan\FlightReservation\Services;

use Hanan\FlightReservation\Models\Flight;
use Illuminate\Support\Collection;

class FlightAvailabilityService
{
   
    public function findAvailableFlights(string $source, string $destination, string $date): Collection
    {
        return Flight::where('source', $source)
            ->where('destination', $destination)
            ->whereDate('departure_time', $date)
            ->get();
    }

   
    public function getAvailableSeats(Flight $flight, string $class = 'economy'): int
    {
        return $flight->seats()
            ->where('class', $class)
            ->where('is_booked', false)
            ->count();
    }
}
