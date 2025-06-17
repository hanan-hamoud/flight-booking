<?php

namespace Hanan\FlightReservation\tests\Unit;

use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException; 

uses(TestCase::class, RefreshDatabase::class);

it('creates the flights table with correct columns and constraints', function () {
    $this->assertTrue(Schema::hasTable('flights'));

    $this->assertTrue(Schema::hasColumn('flights', 'flight_number'));
    $this->assertTrue(Schema::hasColumn('flights', 'departure_airport_id'));
    $this->assertTrue(Schema::hasColumn('flights', 'arrival_airport_id'));
    $this->assertTrue(Schema::hasColumn('flights', 'departure_time'));
    $this->assertTrue(Schema::hasColumn('flights', 'arrival_time'));
    $this->assertTrue(Schema::hasColumn('flights', 'total_economy_seats'));
    $this->assertTrue(Schema::hasColumn('flights', 'total_business_seats'));
    $this->assertTrue(Schema::hasColumn('flights', 'base_economy_price'));
    $this->assertTrue(Schema::hasColumn('flights', 'base_business_price'));
    $this->assertTrue(Schema::hasColumn('flights', 'status'));

    try {
      
        DB::table('flights')->insert([
            'flight_number' => 'FL123',
            'departure_airport_id' => 1,
            'arrival_airport_id' => 2,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(2),
            'total_economy_seats' => 100,
            'total_business_seats' => 20,
            'base_economy_price' => 100.00,
            'base_business_price' => 200.00,
            'status' => 'scheduled'
        ]);

        DB::table('flights')->insert([
            'flight_number' => 'FL123',
            'departure_airport_id' => 3,
            'arrival_airport_id' => 4,
            'departure_time' => now()->addDays(2),
            'arrival_time' => now()->addDays(2)->addHours(2),
            'total_economy_seats' => 100,
            'total_business_seats' => 20,
            'base_economy_price' => 100.00,
            'base_business_price' => 200.00,
            'status' => 'scheduled'
        ]);

        $this->fail('Expected unique constraint violation, but it did not occur.');
    } catch (QueryException $e) { 
        $this->assertEquals('23000', $e->getCode());
        $this->assertStringContainsStringIgnoringCase('flights.flight_number', $e->getMessage());
    }
});


it('sets the default status to scheduled', function () {
    DB::table('flights')->insert([
        'flight_number' => 'FL456',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDay()->addHours(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 20,
        'base_economy_price' => 150.00,
        'base_business_price' => 300.00,
    ]);

    $flight = DB::table('flights')->where('flight_number', 'FL456')->first();
    expect($flight->status)->toBe('scheduled');
});

it('stores base prices as decimal with correct precision', function () {
    DB::table('flights')->insert([
        'flight_number' => 'FL789',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDay()->addHours(3),
        'total_economy_seats' => 100,
        'total_business_seats' => 30,
        'base_economy_price' => 123.45,
        'base_business_price' => 678.90,
        'status' => 'scheduled',
    ]);

    $flight = DB::table('flights')->where('flight_number', 'FL789')->first();

    expect((float) $flight->base_economy_price)->toBe(123.45);
    expect((float) $flight->base_business_price)->toBe(678.90);
});


it('throws error for invalid status value', function () {
    expect(function () {
        DB::table('flights')->insert([
            'flight_number' => 'FL000',
            'departure_airport_id' => 1,
            'arrival_airport_id' => 2,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(2),
            'total_economy_seats' => 100,
            'total_business_seats' => 20,
            'base_economy_price' => 100.00,
            'base_business_price' => 200.00,
            'status' => 'invalid_status',
        ]);
    })->toThrow(QueryException::class);
});

it('drops the flights table when down is called', function () {
    $migration = new \CreateFlightsTable();
    $migration->down();

    expect(Schema::hasTable('flights'))->toBeFalse();
});



