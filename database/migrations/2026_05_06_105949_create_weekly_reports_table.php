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
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->text('ai_summary')->nullable();
            $table->integer('total_sessions')->default(0);
            $table->integer('total_minutes')->default(0);
            $table->integer('stars_earned')->default(0);
            $table->integer('topics_practiced')->default(0);
            $table->json('gaps_detected')->nullable();
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
        Schema::dropIfExists('weekly_reports');
    }
};
