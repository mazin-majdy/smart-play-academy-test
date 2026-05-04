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
        // الإنجازات والأوسمة

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon');
            $table->text('description');
            $table->enum('type', [
                'streak',       // 7 أيام متتالية
                'mastery',      // إتقان موضوع
                'speed',        // سرعة في الإجابة
                'accuracy',     // دقة عالية
                'explorer',     // جرب كل الألعاب
                'social',       // مشاركة مع زملاء
            ]);
            $table->json('condition')->nullable(); // {"streak_days": 7}
            $table->integer('stars_reward')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('child_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at');
            $table->unique(['child_id', 'achievement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('child_achievements');
    }
};
