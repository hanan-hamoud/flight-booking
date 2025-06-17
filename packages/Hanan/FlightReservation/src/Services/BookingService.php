<?php

namespace Hanan\FlightReservation\Services;

use Hanan\FlightReservation\Models\Flight;
use Hanan\FlightReservation\Models\Booking;
use Hanan\FlightReservation\Models\Seat;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookingService
{
    protected FlightAvailabilityService $availabilityService;
    protected PaymentService $paymentService;
    protected TicketService $ticketService;

    public function __construct(
        FlightAvailabilityService $availabilityService,
        PaymentService $paymentService,
        TicketService $ticketService
    ) {
        $this->availabilityService = $availabilityService;
        $this->paymentService = $paymentService;
        $this->ticketService = $ticketService;
    }

    /**
     * Create a booking if seat is available.
     * 
     * @param int $flightId
     * @param string $passengerName
     * @param string $seatClass
     * @param array $paymentDetails
     * @return Booking|null
     * @throws \Exception if seat not available or payment fails
     */
    public function bookFlight(int $flightId, string $passengerName, string $seatClass, array $paymentDetails): Booking
    {
        $flight = Flight::findOrFail($flightId);

        $availableSeat = $flight->seats()
            ->where('class', $seatClass)
            ->where('is_booked', false)
            ->first();

        if (!$availableSeat) {
            throw new \Exception('No available seats in this class.');
        }

        $booking = new Booking();
        $booking->flight_id = $flight->id;
        $booking->passenger_name = $passengerName;
        $booking->seat_id = $availableSeat->id;
        $booking->seat_number = $availableSeat->seat_number;
        $booking->payment_status = 'pending';
        $booking->save();

        $availableSeat->is_booked = true;
        $availableSeat->save();

        $price = $availableSeat->price; 
        $paymentSuccess = $this->paymentService->processPayment($booking, $price, $paymentDetails);

        if (!$paymentSuccess) {
            $availableSeat->is_booked = false;
            $availableSeat->save();
            $booking->delete();

            throw new \Exception('Payment failed.');
        }

        $this->ticketService->generateTicket($booking);

        return $booking;
    }
}
