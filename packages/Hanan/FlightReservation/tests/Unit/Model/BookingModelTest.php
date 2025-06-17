<?php

namespace Hanan\FlightReservation\Tests\Unit\Model;
use Hanan\FlightReservation\Models\Booking;
use Hanan\FlightReservation\Models\BookedSeat;

uses(\Hanan\FlightReservation\Tests\TestCase::class);

it('has the correct fillable properties', function () {
    $model = new BookedSeat();
    expect($model->getFillable())->toEqual(['booking_id', 'seat_id', 'price_at_booking']);
});

it('uses the correct table name', function () {
    $model = new BookedSeat();
    expect($model->getTable())->toBe('booked_seats');
});

it('has incrementing set to false', function () {
    $model = new BookedSeat();
    expect($model->incrementing)->toBeFalse();
});

it('has composite primary key as array', function () {
    $model = new BookedSeat();
    expect($model->getKeyName())->toEqual(['booking_id', 'seat_id']);
});
