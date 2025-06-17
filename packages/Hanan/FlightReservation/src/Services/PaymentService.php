<?php

namespace Hanan\FlightReservation\Services;

use Hanan\FlightReservation\Models\Booking;

class PaymentService
{
    /**
     * Simulate payment process for a booking.
     * 
     * @param Booking $booking
     * @param float $amount
     * @param array $paymentDetails
     * @return bool True if payment successful, false otherwise
     */
    public function processPayment(Booking $booking, float $amount, array $paymentDetails): bool
    {
        
        $booking->payment_status = 'paid';
        $booking->amount_paid = $amount;
        $booking->save();

        return true;
    }

    /**
     * Check if a booking is paid.
     * 
     * @param Booking $booking
     * @return bool
     */
    public function isPaid(Booking $booking): bool
    {
        return $booking->payment_status === 'paid';
    }
}
