<?php

namespace Hanan\FlightReservation\Tests\Unit\Model;

use Hanan\FlightReservation\Models\BookedSeat;

use PHPUnit\Framework\TestCase;

class BookedSeatModelTest extends TestCase
{
    protected BookedSeat $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new BookedSeat();
    }

    public function test_fillable_properties()
    {
        $expected = ['booking_id', 'seat_id', 'price_at_booking'];
        $this->assertEquals($expected, $this->model->getFillable());
    }

    public function test_table_name()
    {
        $this->assertEquals('booked_seats', $this->model->getTable());
    }

    public function test_incrementing_is_false()
    {
        $this->assertFalse($this->model->incrementing);
    }

    public function test_primary_key_is_array()
    {
        $this->assertEquals(['booking_id', 'seat_id'], $this->model->getKeyName());
    }
}
