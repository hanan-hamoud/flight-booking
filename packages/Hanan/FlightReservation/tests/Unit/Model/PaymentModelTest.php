<?php

namespace Hanan\FlightReservation\Tests\Unit\Model;

use Hanan\FlightReservation\Models\Payment;
use Hanan\FlightReservation\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Hanan\FlightReservation\Tests\TestCase::class, RefreshDatabase::class);

it('has the correct fillable properties', function () {
    $payment = new Payment();
    expect($payment->getFillable())->toEqual([
        'booking_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'paid_at',
    ]);
});

it('casts paid_at as a date', function () {
    $payment = new Payment();
    expect($payment->getCasts())->toHaveKey('paid_at');
    expect($payment->getCasts()['paid_at'])->toBe('datetime'); // أو 'date' حسب اللي استخدمته
});


it('has a belongsTo relationship with booking', function () {
    $payment = new Payment();
    $relation = $payment->booking();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($relation->getRelated())->toBeInstanceOf(Booking::class);
    expect($relation->getForeignKeyName())->toBe('booking_id');
});
