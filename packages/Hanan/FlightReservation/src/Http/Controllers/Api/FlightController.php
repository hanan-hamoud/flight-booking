<?php

namespace Hanan\FlightReservation\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Hanan\FlightReservation\Services\FlightAvailabilityService;
use Illuminate\Http\Request;
use Hanan\FlightReservation\Models\Flight;

class FlightController extends Controller
{
    protected FlightAvailabilityService $availabilityService;

    public function __construct(FlightAvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function search(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'destination' => 'required|string',
            'date' => 'required|date',
        ]);

        $flights = $this->availabilityService->findAvailableFlights(
            $request->input('source'),
            $request->input('destination'),
            $request->input('date')
        );

        return response()->json($flights);
    }

    public function index()
    {
        return Flight::all(); 
    }

}
