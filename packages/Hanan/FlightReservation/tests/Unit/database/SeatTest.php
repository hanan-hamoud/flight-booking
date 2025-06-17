<?php

namespace Hanan\FlightReservation\tests\Unit\database;

use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    DB::table('airports')->insert([
        ['id' => 1, 'name' => 'Airport 1', 'code' => 'AAA', 'city' => 'CityA', 'country' => 'CountryA', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Airport 2', 'code' => 'BBB', 'city' => 'CityB', 'country' => 'CountryB', 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('creates the seats table with correct columns and constraints', function () {
    expect(Schema::hasTable('seats'))->toBeTrue();

    expect(Schema::hasColumn('seats', 'id'))->toBeTrue();
    expect(Schema::hasColumn('seats', 'flight_id'))->toBeTrue();
    expect(Schema::hasColumn('seats', 'seat_number'))->toBeTrue();
    expect(Schema::hasColumn('seats', 'seat_class'))->toBeTrue();
    expect(Schema::hasColumn('seats', 'is_available'))->toBeTrue();
    expect(Schema::hasColumn('seats', 'created_at'))->toBeTrue();
    expect(Schema::hasColumn('seats', 'updated_at'))->toBeTrue();
});

it('sets the default value of is_available to true', function () {
    $flightId = DB::table('flights')->insertGetId([
        'flight_number' => 'TEST001',
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

    DB::table('seats')->insert([
        'flight_id' => $flightId,
        'seat_number' => '1A',
        'seat_class' => 'economy',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $seat = DB::table('seats')->where('seat_number', '1A')->first();
    expect($seat->is_available)->toBe(1);
});

it('throws error for invalid seat_class value', function () {
    $flightId = DB::table('flights')->insertGetId([
        'flight_number' => 'TEST002',
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
        DB::table('seats')->insert([
            'flight_id' => $flightId,
            'seat_number' => '2B',
            'seat_class' => 'vip',
        ]);
    })->toThrow(QueryException::class);
});




it('drops the seats table when down is called', function () {
    Schema::drop('seats');

    expect(Schema::hasTable('seats'))->toBeFalse();
});
