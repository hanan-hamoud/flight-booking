<?php

namespace Hanan\FlightReservation\tests\Unit\database;

use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // تفعيل القيود المرجعية في SQLite
    DB::statement('PRAGMA foreign_keys=ON;');

    // إدخال بيانات المطارات
    DB::table('airports')->insert([
        ['id' => 1, 'name' => 'Departure Airport', 'code' => 'DPA', 'city' => 'CityD', 'country' => 'CountryD', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Arrival Airport', 'code' => 'ARA', 'city' => 'CityA', 'country' => 'CountryA', 'created_at' => now(), 'updated_at' => now()],
    ]);

    // إدخال بيانات المستخدم
    if (!Schema::hasTable('users')) {
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    DB::table('users')->insert([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // إدخال بيانات الرحلات
    DB::table('flights')->insert([
        'id' => 1,
        'flight_number' => 'FL101',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => now()->addDay(),
        'arrival_time' => now()->addDays(2),
        'total_economy_seats' => 100,
        'total_business_seats' => 20,
        'base_economy_price' => 120.00,
        'base_business_price' => 240.00,
        'status' => 'scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // إدخال بيانات الحجز
    DB::table('bookings')->insert([
        'id' => 1,
        'booking_reference' => 'BOOKREF123',
        'user_id' => 1,
        'flight_id' => 1,
        'status' => 'confirmed',
        'total_price' => 300.00,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('creates the passengers table with correct columns and constraints', function () {
    expect(Schema::hasTable('passengers'))->toBeTrue();

    foreach ([
        'id', 'booking_id', 'first_name', 'last_name', 'date_of_birth',
        'gender', 'passport_number', 'nationality', 'created_at', 'updated_at'
    ] as $column) {
        expect(Schema::hasColumn('passengers', $column))->toBeTrue();
    }
});

it('can insert a valid passenger record', function () {
    $passengerId = DB::table('passengers')->insertGetId([
        'booking_id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-01-15',
        'gender' => 'male',
        'passport_number' => 'ABC123XYZ',
        'nationality' => 'American',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $passenger = DB::table('passengers')->find($passengerId);

    expect($passenger)->not->toBeNull();
    expect($passenger->first_name)->toBe('John');
    expect($passenger->last_name)->toBe('Doe');
    expect($passenger->passport_number)->toBe('ABC123XYZ');
});

it('allows passport_number to be nullable', function () {
    $passengerId = DB::table('passengers')->insertGetId([
        'booking_id' => 1,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'date_of_birth' => '1985-05-20',
        'gender' => 'female',
        'passport_number' => null,
        'nationality' => 'Canadian',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $passenger = DB::table('passengers')->find($passengerId);

    expect($passenger)->not->toBeNull();
    expect($passenger->passport_number)->toBeNull();
});

it('enforces gender enum values', function () {
    // قيمة صحيحة
    $validPassengerId = DB::table('passengers')->insertGetId([
        'booking_id' => 1,
        'first_name' => 'Valid',
        'last_name' => 'Gender',
        'date_of_birth' => '2000-01-01',
        'gender' => 'other',
        'nationality' => 'British',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $validPassenger = DB::table('passengers')->find($validPassengerId);
    expect($validPassenger->gender)->toBe('other');

    // قيمة غير صحيحة
    expect(function () {
        DB::table('passengers')->insert([
            'booking_id' => 1,
            'first_name' => 'Invalid',
            'last_name' => 'Gender',
            'date_of_birth' => '1995-12-25',
            'gender' => 'non_existent_gender',
            'nationality' => 'German',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});

it('deletes passengers when associated booking is deleted', function () {
    $bookingId = DB::table('bookings')->insertGetId([
        'booking_reference' => 'BOOKREF456',
        'user_id' => 1,
        'flight_id' => 1,
        'status' => 'pending_payment',
        'total_price' => 150.00,
        'booking_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('passengers')->insert([
        [
            'booking_id' => $bookingId,
            'first_name' => 'Passenger1',
            'last_name' => 'Test',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'nationality' => 'Egyptian',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'booking_id' => $bookingId,
            'first_name' => 'Passenger2',
            'last_name' => 'Test',
            'date_of_birth' => '1992-02-02',
            'gender' => 'female',
            'nationality' => 'Egyptian',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    expect(DB::table('passengers')->where('booking_id', $bookingId)->count())->toBe(2);

    DB::table('bookings')->where('id', $bookingId)->delete();

    expect(DB::table('passengers')->where('booking_id', $bookingId)->exists())->toBeFalse();
});

it('drops the passengers table when down is called', function () {
    include_once __DIR__ . '/../../../database/migrations/2025_06_16_063134_create_passengers_table.php';

    $migration = new \CreatePassengersTable();
    $migration->down();
    expect(Schema::hasTable('passengers'))->toBeFalse();

    $migration->up();
    expect(Schema::hasTable('passengers'))->toBeTrue();
});
