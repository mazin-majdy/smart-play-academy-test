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
        // كل جلسة لعب يخوضها الطفل

        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained();

            // حالة الجلسة
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])
                ->default('in_progress');

            // الصعوبة وقت الجلسة
            $table->unsignedTinyInteger('difficulty_used')->default(1);

            // النتائج
            $table->integer('score')->default(0);
            $table->integer('stars_earned')->default(0);
            $table->integer('xp_earned')->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('wrong_count')->default(0);
            $table->integer('hints_used')->default(0);

            // التوقيت
            $table->integer('duration_seconds')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();

            // بيانات Engagement Tracker
            // {"avg_think_time": 8.5, "frustration_signals": 2, "pauses": 1}
            $table->json('engagement_data')->nullable();

            // الأسئلة والإجابات (snapshot كامل)
            // [{"q_id":1,"answer":"4","correct":true,"time":5.2}, ...]
            $table->json('answers_log')->nullable();

            // هل غيّر الـ Adaptive Engine الصعوبة خلال الجلسة؟
            $table->boolean('difficulty_adjusted')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
