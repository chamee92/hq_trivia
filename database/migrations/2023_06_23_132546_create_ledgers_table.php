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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('payment_type_id');
            $table->integer('payment_id');
            $table->string('description');
            $table->string('status');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->decimal('coin_amount', 10, 2);
            $table->decimal('coin_balance', 10, 2);
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
        Schema::dropIfExists('ledgers');
    }
};
