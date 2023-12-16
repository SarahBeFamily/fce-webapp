<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->timestamp('created_at')->primary();
            $table->string('cinema_name');
            $table->string('city');
            $table->string('film_title');
            $table->string('film_genre');
            $table->bigInteger('tickets')->default(0);
            $table->string('food-drink');
            $table->string('prenotazioni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
