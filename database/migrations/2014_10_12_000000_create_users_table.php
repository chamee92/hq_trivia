<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->unsignedBigInteger('user_type_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('email_address');
            $table->string('mobile_number');
            $table->string('login_type');
            $table->string('password');
            $table->string('normal_password');
            $table->string('google_password');
            $table->string('facebook_password');
            $table->string('push_id');
            $table->string('device_id');
            $table->string('os_type');
            $table->string('address1');
            $table->string('address2');
            $table->string('zip_code');
            $table->string('profile_picture');
            $table->string('file_extension');
            $table->decimal('earn_total', 10, 2);
            $table->decimal('pending_withdraw_total', 10, 2);
            $table->decimal('withdraw_total', 10, 2);
            $table->decimal('earn_balance', 10, 2);
            $table->decimal('earn_coin_total', 10, 2);
            $table->decimal('pending_withdraw_coin_total', 10, 2);
            $table->decimal('withdraw_coin_total', 10, 2);
            $table->decimal('earn_coin_balance', 10, 2);
            $table->boolean('is_active')->default(true);
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
};
