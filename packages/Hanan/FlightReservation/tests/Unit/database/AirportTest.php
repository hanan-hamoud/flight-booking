<?php

namespace Hanan\FlightReservation\tests\Unit\Database;

use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Hanan\FlightReservation\Database\Migrations\CreateAirportsTable;

uses(TestCase::class, RefreshDatabase::class);

it('creates the airports table with correct columns and constraints', function () {
    $this->assertTrue(Schema::hasTable('airports'));

    $this->assertTrue(Schema::hasColumn('airports', 'id'));
    $this->assertTrue(Schema::hasColumn('airports', 'name'));
    $this->assertTrue(Schema::hasColumn('airports', 'code'));
    $this->assertTrue(Schema::hasColumn('airports', 'city'));
    $this->assertTrue(Schema::hasColumn('airports', 'country'));
    $this->assertTrue(Schema::hasColumn('airports', 'created_at'));
    $this->assertTrue(Schema::hasColumn('airports', 'updated_at'));
});

it('enforces unique constraint on code column', function () {
    DB::table('airports')->insert([
        'name' => 'Cairo International Airport',
        'code' => 'CAI',
        'city' => 'Cairo',
        'country' => 'Egypt',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(function () {
        DB::table('airports')->insert([
            'name' => 'Fake Cairo Airport',
            'code' => 'CAI', // نفس الكود = يجب أن يفشل
            'city' => 'Cairo',
            'country' => 'Egypt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});

it('can insert valid airport record', function () {
    DB::table('airports')->insert([
        'name' => 'Heathrow Airport',
        'code' => 'LHR',
        'city' => 'London',
        'country' => 'United Kingdom',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $airport = DB::table('airports')->where('code', 'LHR')->first();
    expect($airport)->not->toBeNull();
    expect($airport->city)->toBe('London');
});



it('drops the airports table when down is called', function () {
    Schema::drop('airports');

    expect(Schema::hasTable('airports'))->toBeFalse();
});

