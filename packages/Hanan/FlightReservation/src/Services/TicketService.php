<?php

namespace Hanan\FlightReservation\Services;

use Hanan\FlightReservation\Models\Booking;
use Hanan\FlightReservation\Models\Ticket;
use Illuminate\Support\Str;

class TicketService
{
    /**
     * Generate a ticket for a booking.
     * 
     * @param Booking $booking
     * @return Ticket
     */
    public function generateTicket(Booking $booking): Ticket
    {
        $ticketNumber = 'TKT-' . strtoupper(Str::random(8));

        $ticket = new Ticket();
        $ticket->booking_id = $booking->id;
        $ticket->ticket_number = $ticketNumber;
        $ticket->passenger_name = $booking->passenger_name;
        $ticket->flight_id = $booking->flight_id;
        $ticket->seat_number = $booking->seat_number; 
        $ticket->save();

        return $ticket;
    }
}
