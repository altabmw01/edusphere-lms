<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_classes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('link_type_id')->constrained()->cascadeOnDelete();

            // Denormalized alongside batch_id so a class link can be queried
            // directly by course/book without an extra join through batches.
            $table->morphs('batchable');

            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

            $table->string('full_link');
            $table->string('metting_code')->nullable();
            $table->string('metting_pass_code')->nullable();

            $table->dateTime('class_start_time');
            $table->dateTime('class_end_time');

            $table->text('class_note')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('notified')->default(false);

            $table->timestamps();

            $table->index(['batch_id', 'class_start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_classes');
    }
};
