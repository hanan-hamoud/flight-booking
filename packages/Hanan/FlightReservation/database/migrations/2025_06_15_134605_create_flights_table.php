<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number')->unique();
            $table->foreignId('departure_airport_id')->constrained('airports');
            $table->foreignId('arrival_airport_id')->constrained('airports');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('total_economy_seats');
            $table->integer('total_business_seats');
            $table->decimal('base_economy_price', 10, 2);
            $table->decimal('base_business_price', 10, 2);
            $table->enum('status', ['scheduled', 'departed', 'arrived', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
}
