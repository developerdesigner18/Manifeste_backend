<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->string('user_type');
            $table->string('foundation_name');
            $table->string('address');
            $table->string('country');
            $table->string('province');
            $table->string('foundation_phone');
            $table->string('foundation_website');
            $table->string('foundation_email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('manifestation_phone');
            $table->string('manifestation_email');
            $table->string('login_email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
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
