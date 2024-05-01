<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->nullable();
            $table->string('callsign');
            $table->date('date');
            $table->dateTime('std');
            $table->dateTime('sta');
            $table->string('aircraft_type');
            $table->string('registration');
            $table->string('remarks')->nullable();
            $table->json('locations');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
