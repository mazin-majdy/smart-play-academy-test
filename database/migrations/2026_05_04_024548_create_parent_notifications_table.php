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
        // إشعارات فورية للأهل
        Schema::create('parent_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // الأهل
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();  // الطفل

            $table->enum('type', [
                'frustration_detected', // اكتُشف إحباط
                'limit_reached',        // وصل للحد اليومي
                'achievement',          // فاز بإنجاز
                'streak_broken',        // انقطع الـ streak
                'weekly_report',        // تقرير أسبوعي
                'topic_mastered',       // أتقن موضوعاً
            ]);

            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable(); // بيانات إضافية

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // تقارير أسبوعية (يولّدها AI)
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();

            $table->date('week_start');
            $table->date('week_end');

            // ملخص مولّد بـ AI
            $table->text('ai_summary')->nullable();

            // بيانات التقرير
            $table->integer('total_sessions')->default(0);
            $table->integer('total_minutes')->default(0);
            $table->integer('stars_earned')->default(0);
            $table->integer('topics_practiced')->default(0);

            // الفجوات المكتشفة
            // [{"topic": "الجمع مع التعدي", "mastery": 45, "suggestion": "..."}]
            $table->json('gaps_detected')->nullable();

            // أنشطة واقعية مقترحة
            $table->json('real_activities')->nullable();

            $table->integer('recommended_daily_minutes')->default(30);
            $table->timestamp('generated_at')->nullable();

            $table->unique(['child_id', 'week_start']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_notifications');
        Schema::dropIfExists('weekly_reports');
    }
};
