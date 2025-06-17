<?php

namespace Hanan\FlightReservation\Tests\Unit\Model;

use Hanan\FlightReservation\Models\Flight;
use Hanan\FlightReservation\Models\Airport;
use Hanan\FlightReservation\Models\Seat;
use Hanan\FlightReservation\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
uses(\Hanan\FlightReservation\Tests\TestCase::class, RefreshDatabase::class);

it('has the correct fillable properties', function () {
    $flight = new Flight();
    expect($flight->getFillable())->toEqual([
        'flight_number',
        'departure_airport_id',
        'arrival_airport_id',
        'departure_time',
        'arrival_time',
        'total_economy_seats',
        'total_business_seats',
        'base_economy_price',
        'base_business_price',
        'status',
    ]);
});

it('has a belongsTo relationship with departureAirport', function () {
    $flight = new Flight();
    $relation = $flight->departureAirport();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($relation->getRelated())->toBeInstanceOf(Airport::class);
    expect($relation->getForeignKeyName())->toBe('departure_airport_id');
});

it('has a belongsTo relationship with arrivalAirport', function () {
    $flight = new Flight();
    $relation = $flight->arrivalAirport();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($relation->getRelated())->toBeInstanceOf(Airport::class);
    expect($relation->getForeignKeyName())->toBe('arrival_airport_id');
});

it('has a hasMany relationship with seats', function () {
    $flight = new Flight();
    $relation = $flight->seats();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    expect($relation->getRelated())->toBeInstanceOf(Seat::class);
});

it('has a hasMany relationship with bookings', function () {
    $flight = new Flight();
    $relation = $flight->bookings();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    expect($relation->getRelated())->toBeInstanceOf(Booking::class);
});
