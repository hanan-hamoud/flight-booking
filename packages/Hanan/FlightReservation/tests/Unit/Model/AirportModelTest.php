<?php
namespace Hanan\FlightReservation\Tests\Unit\Model;

use Hanan\FlightReservation\Models\Airport;
use Hanan\FlightReservation\Models\Flight;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Hanan\FlightReservation\Tests\TestCase; 
use Illuminate\Foundation\Testing\RefreshDatabase; 

uses(TestCase::class, RefreshDatabase::class); 

beforeEach(function () {
    $this->model = new Airport();
});

it('has the correct fillable properties', function () {
    expect($this->model->getFillable())
        ->toBe(['name', 'code', 'city', 'country']);
});

it('has a departures relationship as hasMany', function () {
    $relation = $this->model->departures();

    expect($relation)
        ->toBeInstanceOf(HasMany::class)
        ->and($relation->getForeignKeyName())->toBe('departure_airport_id')
        ->and($relation->getRelated())->toBeInstanceOf(Flight::class);
});

it('has an arrivals relationship as hasMany', function () {
    $relation = $this->model->arrivals();

    expect($relation)
        ->toBeInstanceOf(HasMany::class)
        ->and($relation->getForeignKeyName())->toBe('arrival_airport_id')
        ->and($relation->getRelated())->toBeInstanceOf(Flight::class);
});