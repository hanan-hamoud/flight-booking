<?php

namespace Hanan\FlightReservation\Services;

use Hanan\FlightReservation\Models\Flight;
use Hanan\FlightReservation\Models\Booking;
use Hanan\FlightReservation\Models\Seat;

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

    public function bookFlight(int $flightId, string $passengerName, string $seatClass, array $paymentDetails): Booking
    {
        $flight = Flight::findOrFail($flightId);

        $availableSeat = $flight->seats()
            ->where('seat_class', $seatClass)
            ->where('is_available', true)
            ->first();

        if (!$availableSeat) {
            throw new \Exception('No available seats in this class.');
        }

        // حجز المقعد (اجعله غير متوفر)
        $availableSeat->is_available = false;
        $availableSeat->save();

        // السعر (ضع 0 أو قيمة افتراضية إذا لم يكن هناك عمود سعر)
        $price = $availableSeat->price ?? 0;

        // إنشاء حجز
        $booking = new Booking();
        $booking->flight_id = $flight->id;
        $booking->booking_reference = uniqid('BR_');
        $booking->status = 'pending_payment';
        $booking->total_price = $price;
        $booking->booking_date = now();
        $booking->save();

        $paymentSuccess = $this->paymentService->processPayment($booking, $price, $paymentDetails);

        if (!$paymentSuccess) {
            $availableSeat->is_available = true;
            $availableSeat->save();

            $booking->delete();

            throw new \Exception('Payment failed.');
        }

        $this->ticketService->generateTicket($booking);

        return $booking;
    }
}
