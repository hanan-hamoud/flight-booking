<?php

namespace Hanan\FlightReservation\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hanan\FlightReservation\Services\BookingService;
use Hanan\FlightReservation\Models\Booking;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function book(Request $request)
    {
        $validated = $request->validate([
            'flight_id' => 'required|integer|exists:flights,id',
            'passenger_name' => 'required|string|max:255',
            'seat_class' => 'required|string|in:economy,business',
            'payment_details' => 'required|array',
        ]);

        try {
            $booking = $this->bookingService->bookFlight(
                $validated['flight_id'],
                $validated['passenger_name'],
                $validated['seat_class'],
                $validated['payment_details']
            );

            return response()->json($booking, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function ticket(Booking $booking)
    {
        $ticket = $booking->ticket;

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        return response()->json($ticket);
    }

    public function pay(Booking $booking, Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string|unique:payments,transaction_id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'status' => 'in:pending,completed,failed',
            'paid_at' => 'nullable|date',
        ]);

        $payment = $booking->payment()->create([
            'transaction_id' => $validated['transaction_id'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => $validated['status'] ?? 'completed',
            'paid_at' => $validated['paid_at'] ?? now(),
            'currency' => 'USD',
        ]);

        return response()->json($payment, 201);
    }
}
