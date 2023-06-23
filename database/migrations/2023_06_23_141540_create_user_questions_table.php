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
        Schema::create('user_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('challenge_id');
            $table->integer('question_id');
            $table->integer('your_answer')->nullable();
            $table->decimal('earn_amount', 10, 2)->default(0.00);
            $table->decimal('earn_coin', 10, 2)->default(0.00);
            $table->boolean('is_correct')->default(false);
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
        Schema::dropIfExists('user_questions');
    }
};
