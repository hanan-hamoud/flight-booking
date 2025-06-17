<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    DB::statement('PRAGMA foreign_keys=ON;');

    // إدخال بيانات ضرورية للحجوزات والمقاعد
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
        'email' => 'usera@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('bookings')->insert([
        'id' => 1,
        'booking_reference' => 'REF001',
        'user_id' => 1,
        'flight_id' => 1,
        'status' => 'confirmed',
        'total_price' => 500.00,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('seats')->insert([
        'id' => 1,
        'flight_id' => 1,
        'seat_number' => '12A',
        'seat_class' => 'economy',
        'is_available' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('creates the booked_seats table with correct columns', function () {
    expect(Schema::hasTable('booked_seats'))->toBeTrue();

    foreach (['booking_id', 'seat_id', 'price_at_booking', 'created_at', 'updated_at'] as $column) {
        expect(Schema::hasColumn('booked_seats', $column))->toBeTrue();
    }
});

it('allows inserting a valid booked_seat record', function () {
    $inserted = DB::table('booked_seats')->insert([
        'booking_id' => 1,
        'seat_id' => 1,
        'price_at_booking' => 120.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(DB::table('booked_seats')->count())->toBe(1);
});

it('prevents duplicate composite key (booking_id + seat_id)', function () {
    DB::table('booked_seats')->insert([
        'booking_id' => 1,
        'seat_id' => 1,
        'price_at_booking' => 120.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(function () {
        DB::table('booked_seats')->insert([
            'booking_id' => 1,
            'seat_id' => 1,
            'price_at_booking' => 130.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});

it('deletes booked_seats when booking is deleted', function () {
    DB::table('booked_seats')->insert([
        'booking_id' => 1,
        'seat_id' => 1,
        'price_at_booking' => 110.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('bookings')->where('id', 1)->delete();

    expect(DB::table('booked_seats')->count())->toBe(0);
});

it('deletes booked_seats when seat is deleted', function () {
    DB::table('booked_seats')->insert([
        'booking_id' => 1,
        'seat_id' => 1,
        'price_at_booking' => 110.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('seats')->where('id', 1)->delete();

    expect(DB::table('booked_seats')->count())->toBe(0);
});

it('drops the booked_seats table on down', function () {
    Schema::dropIfExists('booked_seats');

    expect(Schema::hasTable('booked_seats'))->toBeFalse();
});
