<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFceUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('user_firstname')->nullable();
            $table->string('user_lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('user_birthdate')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('user_address')->nullable();
            $table->string('user_city')->nullable();
            $table->string('user_zip')->nullable();
            $table->string('user_province')->nullable();
            $table->string('user_country')->nullable();
            $table->string('user_fiscalcode')->nullable();
            $table->string('favorite_fce')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
