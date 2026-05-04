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
        // الطفل له profile مستقل — مش account بالمعنى الكامل
        // الأهل والمعلمين هم من يُسجّلونهم
        Schema::create('children', function (Blueprint $table) {
            $table->id();

            // المعرّف الأساسي
            $table->string('name');
            $table->string('username')->unique();    // اسم الدخول للطفل
            $table->string('password');               // كلمة مرور بسيطة
            $table->string('avatar')->default('default');
            $table->string('avatar_color')->default('#FF6B6B'); // لون الشخصية

            // الفئة العمرية (يحدد نمط الألعاب تلقائياً)
            $table->date('birthdate')->nullable();
            $table->enum('age_group', ['6-8', '9-11', '12-14'])->default('9-11');

            // الأهل المرتبطين (قد يكون أكثر من أحد)
            // نستخدم pivot table لاحقاً

            // إعدادات التعلم
            $table->string('preferred_language')->default('ar'); // ar / en
            $table->enum('learning_style', ['visual', 'auditory', 'mixed'])
                ->default('mixed');

            // نقاط اللعب
            $table->integer('total_stars')->default(0);
            $table->integer('total_xp')->default(0);
            $table->integer('current_level')->default(1);
            $table->integer('streak_days')->default(0);      // أيام متتالية
            $table->date('last_play_date')->nullable();

            // حدود الاستخدام (يضبطها الأهل)
            $table->integer('daily_limit_minutes')->default(60);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
