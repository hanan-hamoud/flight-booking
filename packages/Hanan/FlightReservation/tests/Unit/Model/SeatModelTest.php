<?php

namespace Hanan\FlightReservation\Tests\Unit\Model;

use Hanan\FlightReservation\Models\Seat;
use Hanan\FlightReservation\Models\Flight;
use Hanan\FlightReservation\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Hanan\FlightReservation\Tests\TestCase::class, RefreshDatabase::class);

it('has the correct fillable properties', function () {
    $seat = new Seat();
    expect($seat->getFillable())->toEqual([
        'flight_id',
        'seat_number',
        'seat_class',
        'is_available',
    ]);
});

it('casts is_available as boolean', function () {
    $seat = new Seat();
    expect($seat->getCasts())->toHaveKey('is_available');
    expect($seat->getCasts()['is_available'])->toBe('boolean');
});

it('has a belongsTo relationship with flight', function () {
    $seat = new Seat();
    $relation = $seat->flight();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($relation->getRelated())->toBeInstanceOf(Flight::class);
    expect($relation->getForeignKeyName())->toBe('flight_id');
});

it('has a belongsToMany relationship with bookings with pivot price_at_booking', function () {
    $seat = new Seat();
    $relation = $seat->bookings();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
    expect($relation->getRelated())->toBeInstanceOf(Booking::class);
    expect($relation->getPivotColumns())->toContain('price_at_booking');
    expect($relation->getTable())->toBe('booked_seats');
});
