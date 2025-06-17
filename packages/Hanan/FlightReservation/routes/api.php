<?php 
use Hanan\FlightReservation\Http\Controllers\Api\FlightController;
use Hanan\FlightReservation\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;







Route::prefix('api')->group(function () {
    Route::get('/flights', [FlightController::class, 'index']); 
   Route::post('/payments/{booking}', [BookingController::class, 'pay']);
   Route::post('/bookings', [BookingController::class, 'book']);
   Route::get('/tickets/{booking}', [BookingController::class, 'ticket']);
});