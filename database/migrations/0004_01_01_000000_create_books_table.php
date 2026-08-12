<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('added_by')->constrained('users')->restrictOnDelete();
            $table->string('thumbnail')->nullable();
            $table->string('cover')->nullable();
            $table->string('pdf_path')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->unsignedInteger('pages')->default(0);
            $table->string('language', 40)->default('English');
            $table->string('publisher')->nullable();
            $table->string('edition')->nullable();
            $table->string('isbn', 40)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->enum('status', ['draft', 'published'])->default('draft')->index();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->unsignedInteger('downloads_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
