<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels');
            $table->string('language', 40)->default('English');
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('lessons_count')->default(0);
            $table->boolean('has_certificate')->default(true);
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('target_audience')->nullable();
            $table->longText('what_you_will_learn')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_trending')->default(false)->index();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('students_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
			$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'is_featured']);
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['video', 'text', 'pdf', 'quiz'])->default('video');
            $table->string('content_path')->nullable();
            $table->longText('content_text')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('course_resources');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('courses');
    }
};
