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
        // محادثات الطفل مع الـ AI Tutor

        Schema::create('ai_tutor_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();

            // المحادثة كاملة
            // [{"role":"user","content":"..."}, {"role":"assistant","content":"..."}]
            $table->json('messages');

            // إحصائيات
            $table->integer('message_count')->default(0);
            $table->integer('tokens_used')->default(0);
            $table->string('ai_model')->default('gpt-4o-mini');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tutor_chats');
    }
};
