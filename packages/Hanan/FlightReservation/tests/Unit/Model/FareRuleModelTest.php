<?php

namespace Hanan\FlightReservation\Tests\Unit\Model;

use Hanan\FlightReservation\Models\FareRule;

uses(\Hanan\FlightReservation\Tests\TestCase::class);

it('has the correct fillable properties', function () {
    $model = new FareRule();
    expect($model->getFillable())->toEqual([
        'rule_name',
        'description',
        'applies_to_class',
        'cancellation_deadline_hours',
    ]);
});

it('can create a fare rule with fillable attributes', function () {
    $data = [
        'rule_name' => 'No Refund',
        'description' => 'No refund after 24 hours before flight',
        'applies_to_class' => 'economy',
        'cancellation_deadline_hours' => 24,
    ];

    $fareRule = FareRule::create($data);

    expect($fareRule->rule_name)->toBe($data['rule_name']);
    expect($fareRule->description)->toBe($data['description']);
    expect($fareRule->applies_to_class)->toBe($data['applies_to_class']);
    expect($fareRule->cancellation_deadline_hours)->toBe($data['cancellation_deadline_hours']);
});
