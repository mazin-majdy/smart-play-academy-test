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
        Schema::create('game_session_answers', function (Blueprint $table) {
            $table->id();

            // ✅ العلاقات
            $table->foreignId('game_session_id')
                ->constrained('game_sessions')
                ->onDelete('cascade'); // إذا الجلسة اتحذفت، تحذف الإجابات تبعها

            $table->foreignId('question_id')
                ->constrained('questions')
                ->onDelete('cascade'); // إذا السؤال اتحذف، تحذف الإجابات تبعه

            $table->foreignId('child_id')
                ->constrained('children')
                ->onDelete('cascade'); // للرجوع السريع لإجابات الطفل

            // ✅ بيانات الإجابة
            $table->text('given_answer')->nullable(); // الإجابة اللي كتبها/اختارها الطفل
            $table->text('correct_answer')->nullable(); // الإجابة الصحيحة (للتخزين السريع)

            $table->boolean('is_correct'); // هل الإجابة صحيحة؟

            $table->integer('time_taken_seconds')->default(0); // وقت الإجابة بالثواني
            $table->integer('points_earned')->default(0); // النقاط اللي كسبها من هالسؤال

            $table->string('difficulty_level')->nullable(); // مستوى صعوبة السؤال (Easy, Medium, Hard)

            // ✅ تحليل الإجابة (لـ AI أو التحليلات)
            $table->json('metadata')->nullable(); // بيانات إضافية مثل: اختيارات متعددة، محاولات، etc.

            // ✅ التوقيت
            $table->timestamp('answered_at')->useCurrent(); // وقت الإجابة الدقيق
            $table->timestamps();

            // ✅ الفهارس لتحسين الأداء
            $table->index(['game_session_id', 'child_id']);
            $table->index(['question_id', 'is_correct']);
            $table->index(['answered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_session_answers');
    }
};
