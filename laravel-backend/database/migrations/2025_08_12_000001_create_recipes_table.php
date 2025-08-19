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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->foreignId('karenderia_id')->constrained()->onDelete('cascade');
            $table->json('ingredients'); // Detailed ingredients with amounts, units, notes
            $table->json('instructions'); // Step-by-step cooking instructions
            $table->integer('prep_time_minutes');
            $table->integer('cook_time_minutes');
            $table->enum('difficulty_level', ['easy', 'medium', 'hard']);
            $table->integer('servings');
            $table->string('category');
            $table->string('cuisine_type');
            $table->decimal('cost_estimate', 8, 2);
            $table->json('nutritional_info')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_signature')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('times_cooked')->default(0);
            $table->timestamps();
            
            $table->index(['karenderia_id', 'category']);
            $table->index(['cuisine_type', 'difficulty_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
