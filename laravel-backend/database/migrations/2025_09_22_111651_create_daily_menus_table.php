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
        Schema::create('daily_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karenderia_id')->constrained('karenderias')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->date('date'); // The date this menu item is available
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']); // Meal category
            $table->integer('quantity')->default(0); // Number of servings available
            $table->integer('original_quantity')->default(0); // Original quantity set (for tracking)
            $table->boolean('is_available')->default(true); // Can be turned on/off by owner
            $table->decimal('special_price', 8, 2)->nullable(); // Optional special price for the day
            $table->text('notes')->nullable(); // Optional notes (e.g., "Fresh catch of the day")
            $table->timestamps();
            
            // Ensure unique combination of karenderia, menu item, date, and meal type
            $table->unique(['karenderia_id', 'menu_item_id', 'date', 'meal_type'], 'daily_menu_unique');
            
            // Indexes for faster queries
            $table->index(['date', 'meal_type']);
            $table->index(['karenderia_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_menus');
    }
};
