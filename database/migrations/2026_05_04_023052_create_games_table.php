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
        // أنواع الألعاب وإعداداتها

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();

            // نوع اللعبة — يحدد الـ JS component المستخدم
            $table->enum('game_type', [
                'drag_drop',        // سحب وإفلات (6-8)
                'visual_match',     // مطابقة بصرية (6-8)
                'story_interactive', // قصة تفاعلية (6-8)
                'sound_guess',      // تخمين من الصوت (6-8)
                'math_puzzle',      // لغز رياضي (9-11)
                'logic_chain',      // سلسلة منطقية (9-11)
                'word_challenge',   // تحدي كلمات (9-11)
                'virtual_lab',      // مختبر افتراضي (9-11)
                'strategy_game',    // لعبة استراتيجية (12-14)
                'science_sim',      // محاكاة علمية (12-14)
                'block_code',       // برمجة بلوكات (12-14)
                'timed_challenge',  // تحدي زمني (12-14)
                'quiz',             // أسئلة وأجوبة (كل الفئات)
            ]);

            // الفئة العمرية
            $table->enum('age_group', ['6-8', '9-11', '12-14']);

            // إعدادات اللعبة (JSON مرن)
            // مثال: {"time_limit": 60, "lives": 3, "hints_allowed": true}
            $table->json('config')->nullable();

            // مستوى الصعوبة الافتراضي 1-5
            $table->unsignedTinyInteger('difficulty')->default(1);

            // النقاط والمكافآت
            $table->integer('stars_reward')->default(10);
            $table->integer('xp_reward')->default(50);

            // هل تولّد AI أسئلتها؟
            $table->boolean('ai_generated')->default(false);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
