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
        Schema::create('fce_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamp('order_created_at');
            $table->string('order_type');
            $table->string('order_status');
            $table->string('order_data_list');
            $table->string('order_amount');
            $table->string('order_transaction');
            $table->string('order_ref_cinema');
            $table->text('order_notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fce_orders');
    }
};
