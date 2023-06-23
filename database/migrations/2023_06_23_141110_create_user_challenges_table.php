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
        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('challenge_id');
            $table->boolean('has_watched')->default(false);
            $table->boolean('has_like')->default(false);
            $table->boolean('has_attend_quiz')->default(false);
            $table->integer('correct_answer_count')->default(0);
            $table->integer('wrong_answer_count')->default(0);
            $table->decimal('earn_amount', 10, 2)->default(0.00);
            $table->decimal('earn_coin', 10, 2)->default(0.00);
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
        Schema::dropIfExists('user_challenges');
    }
};
