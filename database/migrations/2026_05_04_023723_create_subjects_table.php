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
        // المواد: رياضيات / لغة / علوم / تفكير منطقي
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // الرياضيات
            $table->string('name_en')->nullable();
            $table->string('icon');            // emoji أو اسم أيقونة
            $table->string('color');           // لون المادة
            $table->enum('age_groups', ['all', '6-8', '9-11', '12-14'])
                ->default('all');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // الموضوعات: جمع / طرح / ضرب / قسمة...
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_topic_id')
                ->nullable()
                ->constrained('topics')
                ->nullOnDelete(); // للموضوعات الفرعية

            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();

            // الصعوبة 1-5
            $table->unsignedTinyInteger('difficulty_level')->default(1);

            // الفئة العمرية المناسبة
            $table->enum('age_group', ['6-8', '9-11', '12-14'])->default('9-11');

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('topics');
    }
};
