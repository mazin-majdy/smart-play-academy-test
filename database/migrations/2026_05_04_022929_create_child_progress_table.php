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
        // مستوى الطفل في كل موضوع

        Schema::create('child_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();

            // مستوى الإتقان 0-100
            $table->unsignedTinyInteger('mastery_score')->default(0);

            // مستوى الصعوبة الحالي للطفل في هذا الموضوع (1-5)
            $table->unsignedTinyInteger('current_difficulty')->default(1);

            // إحصائيات
            $table->integer('sessions_count')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('total_time_seconds')->default(0);
            $table->integer('hints_used')->default(0);

            // نقاط القوة والضعف (يحدّثها Adaptive Engine)
            // مثال: {"strengths": ["addition"], "weaknesses": ["subtraction_carry"]}
            $table->json('performance_data')->nullable();

            // آخر جلسة
            $table->timestamp('last_played_at')->nullable();
            $table->timestamp('completed_at')->nullable(); // إذا أتقن الموضوع

            $table->timestamps();
            $table->unique(['child_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_progress');
    }
};
