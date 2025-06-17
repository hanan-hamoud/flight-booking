<?php

namespace Hanan\FlightReservation\tests\Unit\database;

use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

uses(TestCase::class, RefreshDatabase::class);

it('creates the bookings table with correct columns and constraints', function () {
    expect(Schema::hasTable('bookings'))->toBeTrue();

    expect(Schema::hasColumn('bookings', 'id'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'booking_reference'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'user_id'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'flight_id'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'status'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'total_price'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'booking_date'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'created_at'))->toBeTrue();
    expect(Schema::hasColumn('bookings', 'updated_at'))->toBeTrue();
});

it('sets default status to pending_payment', function () {
    $userId = DB::table('users')->insertGetId([
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $flightId = DB::table('flights')->insertGetId([
        'flight_number' => 'FL123',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDays(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 20,
        'base_economy_price' => 120,
        'base_business_price' => 240,
        'status' => 'scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $bookingId = DB::table('bookings')->insertGetId([
        'booking_reference' => 'BR123456',
        'user_id' => $userId,
        'flight_id' => $flightId,
        'total_price' => 360.00,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $booking = DB::table('bookings')->where('id', $bookingId)->first();

    expect($booking->status)->toBe('pending_payment');
});

it('throws error for invalid status value', function () {
    $flightId = DB::table('flights')->insertGetId([
        'flight_number' => 'FL999',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDays(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 20,
        'base_economy_price' => 120,
        'base_business_price' => 240,
        'status' => 'scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(function () use ($flightId) {
        DB::table('bookings')->insert([
            'booking_reference' => 'BR999999',
            'user_id' => null,
            'flight_id' => $flightId,
            'status' => 'invalid_status', 
            'total_price' => 100,
            'booking_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});


it('allows user_id to be nullable and sets to null on user deletion', function () {

    $userId = DB::table('users')->insertGetId([
        'name' => 'Delete User',
        'email' => 'deleteuser@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $flightId = DB::table('flights')->insertGetId([
        'flight_number' => 'FLDEL',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDays(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 20,
        'base_economy_price' => 120,
        'base_business_price' => 240,
        'status' => 'scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $bookingId = DB::table('bookings')->insertGetId([
        'booking_reference' => 'BRDEL001',
        'user_id' => $userId,
        'flight_id' => $flightId,
        'status' => 'confirmed',
        'total_price' => 240,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('users')->where('id', $userId)->delete();

    $booking = DB::table('bookings')->where('id', $bookingId)->first();

    expect($booking->user_id)->toBeNull();
});

it('throws error if booking_reference is not unique', function () {
    $userId = DB::table('users')->insertGetId([
        'name' => 'User Unique',
        'email' => 'uniqueuser@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $flightId = DB::table('flights')->insertGetId([
        'flight_number' => 'FLUNQ',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDays(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 20,
        'base_economy_price' => 120,
        'base_business_price' => 240,
        'status' => 'scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('bookings')->insert([
        'booking_reference' => 'BRUNIQUE',
        'user_id' => $userId,
        'flight_id' => $flightId,
        'status' => 'confirmed',
        'total_price' => 300,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(function () use ($flightId) { 
        DB::table('bookings')->insert([
            'booking_reference' => 'BRUNIQUE',
            'user_id' => null,
            'flight_id' => $flightId,
            'status' => 'pending_payment',
            'total_price' => 150,
            'booking_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});

it('deletes bookings when flight is deleted', function () {
    DB::statement('PRAGMA foreign_keys=ON;');

    $userId = DB::table('users')->insertGetId([
        'name' => 'Flight Delete User',
        'email' => 'flightdeleteuser@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $flightId = DB::table('flights')->insertGetId([
        'flight_number' => 'FLDEL001',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDays(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 20,
        'base_economy_price' => 120,
        'base_business_price' => 240,
        'status' => 'scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('bookings')->insert([
        'booking_reference' => 'BRDEL123',
        'user_id' => $userId,
        'flight_id' => $flightId,
        'status' => 'confirmed',
        'total_price' => 360,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('flights')->where('id', $flightId)->delete();

    expect(DB::table('bookings')->where('booking_reference', 'BRDEL123')->exists())->toBeFalse();
});

