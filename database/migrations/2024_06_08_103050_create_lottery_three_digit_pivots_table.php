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
        Schema::create('lottery_three_digit_pivots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('threed_setting_id')->nullable();
            $table->unsignedBigInteger('lotto_id');
            $table->unsignedBigInteger('three_digit_id');
            $table->unsignedBigInteger('threed_match_time_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('bet_digit');
            $table->integer('sub_amount')->default(0);
            $table->boolean('prize_sent')->default(false);
            $table->string('match_status');
            $table->date('res_date');
            $table->time('res_time')->nullable();
            $table->date('match_start_date')->nullable();
            $table->string('result_number')->nullable();
            $table->boolean('win_lose')->default(false);
            $table->date('play_date')->default('2024-5-20');
            $table->time('play_time')->default('15:30:00');
            $table->string('running_match')->default('2024-01-16');
            //$table->string('match_name')->default('1');
            //$table->enum('admin_log', ['open', 'closed'])->default('open'); // New status column
            //$table->enum('user_log', ['open', 'closed'])->default('open'); // New status column
            $table->foreign('threed_setting_id')->references('id')->on('threed_settings')->onDelete('cascade');
            $table->foreign('lotto_id')->references('id')->on('lottos')->onDelete('cascade');
            $table->foreign('threed_match_time_id')->references('id')->on('threed_match_times')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_three_digit_pivots');
    }
};
