<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            // Polymorphic: a batch belongs to either a Course or a Book,
            // matching the existing purchasable/reviewable/wishlistable pattern.
            $table->morphs('batchable');

            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('batch_level_id')->nullable()->constrained()->nullOnDelete();

            $table->string('batch_number');
            $table->string('batch_name');

            // Daily class time window (recurring every batch_days day, not a specific date).
            $table->time('class_start_time');
            $table->time('class_end_time');

            // e.g. ["Fri","Sat","Sun","Mon"] — matched against now()->format('D')
            $table->json('batch_days');
            $table->unsignedTinyInteger('weekly_days')->default(1);

            $table->date('batch_started_date');
            $table->date('batch_end_date')->nullable();

            $table->unsignedInteger('student_limit')->default(30);
            $table->boolean('free_or_paid')->default(1); // 1 = paid, 0 = free
            $table->boolean('upcoming_status')->default(0);
            $table->boolean('hide_batch')->default(0);

            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['teacher_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
