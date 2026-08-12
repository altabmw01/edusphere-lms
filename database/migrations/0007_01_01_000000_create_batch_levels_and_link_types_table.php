<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Level 1, Level 2, Level 3, Advanced Level
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('link_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Zoom, Google Meet
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_types');
        Schema::dropIfExists('batch_levels');
    }
};
