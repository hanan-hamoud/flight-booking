<?php 
use Hanan\FlightReservation\Http\Controllers\Api\FlightController;
use Hanan\FlightReservation\Http\Controllers\Api\BookingController;



Route::get('/flights', [FlightController::class, 'search']);
Route::post('/bookings', [BookingController::class, 'book']);
Route::post('/payments/{booking}', [BookingController::class, 'pay']);
Route::get('/tickets/{booking}', [BookingController::class, 'ticket']);
