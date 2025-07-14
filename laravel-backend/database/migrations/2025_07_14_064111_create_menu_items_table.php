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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karenderia_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('category');
            $table->string('image_url')->nullable();
            $table->json('ingredients')->nullable();
            $table->json('allergens')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('preparation_time')->nullable(); // in minutes
            $table->integer('calories')->nullable();
            $table->integer('spicy_level')->default(0); // 0-5 scale
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
