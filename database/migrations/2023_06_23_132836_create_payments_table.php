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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('invoice_number');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->decimal('coin_amount', 10, 2);
            $table->decimal('paid_coin_amount', 10, 2);
            $table->string('status');
            $table->text('transaction_data');
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
        Schema::dropIfExists('payments');
    }
};
