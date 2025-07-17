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
        Schema::table('meal_plans', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['name', 'description', 'meals', 'is_active']);
            
            // Add new columns for daily meals
            $table->string('breakfast')->nullable();
            $table->string('lunch')->nullable();
            $table->string('dinner')->nullable();
            $table->date('plan_date')->default(now()->format('Y-m-d'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_plans', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['breakfast', 'lunch', 'dinner', 'plan_date']);
            
            // Add back old columns
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('meals')->nullable();
            $table->boolean('is_active')->default(false);
        });
    }
};
