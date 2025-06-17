<?php

namespace Hanan\FlightReservation\Services;

use Hanan\FlightReservation\Models\Booking;

class PaymentService
{
    /**
     * Process payment for a booking by creating a Payment record
     * and updating the booking’s status.
     *
     * @param Booking $booking
     * @param float   $amount
     * @param array   $paymentDetails
     * @return bool
     */
    public function processPayment(Booking $booking, float $amount, array $paymentDetails): bool
    {
        try {
            // Create the payment record
            $booking->payment()->create([
                'transaction_id'   => $paymentDetails['transaction_id'] ?? uniqid('TX_'),
                'amount'           => $amount,
                'currency'         => $paymentDetails['currency'] ?? 'USD',
                'status'           => $paymentDetails['status']   ?? 'completed',
                'payment_method'   => $paymentDetails['payment_method'] ?? 'unknown',
                'paid_at'          => $paymentDetails['paid_at']  ?? now(),
            ]);

            // Update the booking status
            $booking->status = 'confirmed';
            $booking->save();

            return true;
        } catch (\Exception $e) {
            // Optionally log $e->getMessage()
            return false;
        }
    }

    /**
     * Check if a booking has a completed payment.
     *
     * @param Booking $booking
     * @return bool
     */
    public function isPaid(Booking $booking): bool
    {
        return $booking->payment && $booking->payment->status === 'completed';
    }
}
