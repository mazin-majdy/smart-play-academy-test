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
        // الأسئلة المرتبطة بكل لعبة (static + AI-generated)

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->restrictOnDelete();

            $table->text('content');            // نص السؤال
            $table->string('content_type')
                ->default('text');            // text / image / audio / equation
            $table->string('media_path')->nullable(); // صورة أو صوت

            // الإجابات (JSON)
            // مثال: [{"id":1,"text":"4","is_correct":true},{"id":2,"text":"5","is_correct":false}]
            $table->json('answers');

            // نوع الإجابة
            $table->enum('answer_type', [
                'single_choice',   // اختيار واحد
                'multiple_choice', // اختيار متعدد
                'drag_drop',       // سحب وإفلات
                'fill_blank',      // ملء الفراغ
                'order_steps',     // ترتيب خطوات
                'matching',        // مطابقة
            ])->default('single_choice');

            $table->unsignedTinyInteger('difficulty')->default(1);

            // تفسير الإجابة (للـ AI Tutor)
            $table->text('explanation')->nullable();
            $table->text('hint')->nullable();

            // هل ولّدها AI؟
            $table->boolean('ai_generated')->default(false);
            $table->string('ai_model')->nullable(); // gpt-4o / claude-3...

            $table->integer('times_used')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0); // نسبة النجاح

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
