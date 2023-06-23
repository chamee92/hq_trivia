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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('challenge_name');
            $table->text('challenge_description');
            $table->string('image_path');
            $table->string('video_path');
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->integer('video_duration');
            $table->integer('quiz_duration');
            $table->integer('question_duration');
            $table->dateTime('question_start_time');
            $table->dateTime('question_end_time');
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_coin', 10, 2);
            $table->integer('question_count');
            $table->decimal('question_price', 10, 2);
            $table->decimal('question_coin', 10, 2);
            $table->integer('total_watch');
            $table->integer('total_like');
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
        Schema::dropIfExists('challenges');
    }
};
