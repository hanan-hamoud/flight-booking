<?php

namespace Hanan\FlightReservation\Tests\Unit\Model;

use Hanan\FlightReservation\Models\Passenger;
use Hanan\FlightReservation\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Hanan\FlightReservation\Tests\TestCase::class, RefreshDatabase::class);

it('has the correct fillable properties', function () {
    $passenger = new Passenger();
    expect($passenger->getFillable())->toEqual([
        'booking_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'passport_number',
        'nationality',
    ]);
});

it('casts date_of_birth as a date', function () {
    $passenger = new Passenger();
    expect($passenger->getCasts())->toHaveKey('date_of_birth');
    expect($passenger->getCasts()['date_of_birth'])->toBe('date');
});


it('has a belongsTo relationship with booking', function () {
    $passenger = new Passenger();
    $relation = $passenger->booking();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($relation->getRelated())->toBeInstanceOf(Booking::class);
    expect($relation->getForeignKeyName())->toBe('booking_id');
});
