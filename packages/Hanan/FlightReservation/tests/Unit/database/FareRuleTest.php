<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Hanan\FlightReservation\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('creates the fare_rules table with correct columns', function () {
    expect(Schema::hasTable('fare_rules'))->toBeTrue();

    $columns = ['id', 'rule_name', 'description', 'applies_to_class', 'cancellation_deadline_hours', 'created_at', 'updated_at'];

    foreach ($columns as $column) {
        expect(Schema::hasColumn('fare_rules', $column))->toBeTrue();
    }
});

it('inserts a valid fare rule', function () {
    DB::table('fare_rules')->insert([
        'rule_name' => 'No refund after 24h',
        'description' => 'Tickets are non-refundable 24 hours before departure.',
        'applies_to_class' => 'economy',
        'cancellation_deadline_hours' => 24,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $rule = DB::table('fare_rules')->first();

    expect($rule->rule_name)->toBe('No refund after 24h');
    expect($rule->applies_to_class)->toBe('economy');
    expect($rule->cancellation_deadline_hours)->toBe(24);
});

it('accepts nullable description and cancellation_deadline_hours', function () {
    DB::table('fare_rules')->insert([
        'rule_name' => 'Flexible fare',
        'applies_to_class' => 'business',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $rule = DB::table('fare_rules')->where('rule_name', 'Flexible fare')->first();

    expect($rule->description)->toBeNull();
    expect($rule->cancellation_deadline_hours)->toBeNull();
});

it('throws error for invalid applies_to_class', function () {
    expect(function () {
        DB::table('fare_rules')->insert([
            'rule_name' => 'Invalid Class',
            'applies_to_class' => 'firstclass', // غير مسموح
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })->toThrow(QueryException::class);
});

it('drops the fare_rules table on down', function () {
    Schema::dropIfExists('fare_rules');
    expect(Schema::hasTable('fare_rules'))->toBeFalse();
});
