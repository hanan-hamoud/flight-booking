<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    DB::statement('PRAGMA foreign_keys=ON;');

    DB::table('airports')->insert([
        ['id' => 1, 'name' => 'Airport A', 'code' => 'AAA', 'city' => 'CityA', 'country' => 'CountryA', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Airport B', 'code' => 'BBB', 'city' => 'CityB', 'country' => 'CountryB', 'created_at' => now(), 'updated_at' => now()],
    ]);

    DB::table('flights')->insert([
        'id' => 1,
        'flight_number' => 'FL123',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDays(1),
        'arrival_time' => now()->addDays(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 10,
        'base_economy_price' => 100.00,
        'base_business_price' => 200.00,
        'status' => 'scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('users')->insert([
        'id' => 1,
        'name' => 'User A',
        'email' => 'user@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('bookings')->insert([
        'id' => 1,
        'booking_reference' => 'REFPAY123',
        'user_id' => 1,
        'flight_id' => 1,
        'status' => 'confirmed',
        'total_price' => 300.00,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('creates the payments table with correct columns', function () {
    expect(Schema::hasTable('payments'))->toBeTrue();

    $columns = [
        'id', 'booking_id', 'transaction_id', 'amount', 'currency',
        'status', 'payment_method', 'paid_at', 'created_at', 'updated_at'
    ];

    foreach ($columns as $column) {
        expect(Schema::hasColumn('payments', $column))->toBeTrue();
    }
});

it('allows inserting a valid payment record', function () {
    DB::table('payments')->insert([
        'booking_id' => 1,
        'transaction_id' => 'TX123456',
        'amount' => 300.00,
        'currency' => 'USD',
        'status' => 'completed',
        'payment_method' => 'credit_card',
        'paid_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(DB::table('payments')->count())->toBe(1);
});

it('prevents duplicate transaction_id', function () {
    DB::table('payments')->insert([
        'booking_id' => 1,
        'transaction_id' => 'TXDUPLICATE',
        'amount' => 300.00,
        'currency' => 'USD',
        'status' => 'completed',
        'payment_method' => 'credit_card',
        'paid_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(function () {
        DB::table('payments')->insert([
            'booking_id' => 1,
            'transaction_id' => 'TXDUPLICATE', // مكرر
            'amount' => 200.00,
            'currency' => 'EUR',
            'status' => 'pending',
            'payment_method' => 'paypal',
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});

it('sets default values for currency and status', function () {
    DB::table('payments')->insert([
        'booking_id' => 1,
        'transaction_id' => 'TXDEFAULT',
        'amount' => 250.00,
        'payment_method' => 'paypal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $payment = DB::table('payments')->where('transaction_id', 'TXDEFAULT')->first();

    expect($payment->currency)->toBe('USD');
    expect($payment->status)->toBe('pending');
});

it('deletes payments when booking is deleted', function () {
    DB::table('payments')->insert([
        'booking_id' => 1,
        'transaction_id' => 'TXDELETE',
        'amount' => 120.00,
        'currency' => 'USD',
        'status' => 'completed',
        'payment_method' => 'bank_transfer',
        'paid_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('bookings')->where('id', 1)->delete();

    expect(DB::table('payments')->count())->toBe(0);
});

it('drops the payments table on down', function () {
    Schema::dropIfExists('payments');

    expect(Schema::hasTable('payments'))->toBeFalse();
});
