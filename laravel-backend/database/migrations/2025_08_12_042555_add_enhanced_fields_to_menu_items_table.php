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
        Schema::table('menu_items', function (Blueprint $table) {
            // Add nutrition information columns if they don't exist
            if (!Schema::hasColumn('menu_items', 'protein')) {
                $table->integer('protein')->nullable()->after('calories');
            }
            if (!Schema::hasColumn('menu_items', 'carbs')) {
                $table->integer('carbs')->nullable()->after('protein');
            }
            if (!Schema::hasColumn('menu_items', 'fat')) {
                $table->integer('fat')->nullable()->after('carbs');
            }
            
            // Add dietary tags column if it doesn't exist
            if (!Schema::hasColumn('menu_items', 'dietary_tags')) {
                $table->json('dietary_tags')->nullable()->after('allergens');
            }
            
            // Add spiciness level if it doesn't exist
            if (!Schema::hasColumn('menu_items', 'spiciness_level')) {
                $table->integer('spiciness_level')->nullable()->default(1)->after('dietary_tags');
            }
            
            // Add preparation time if it doesn't exist (already exists as preparation_time_minutes)
            if (!Schema::hasColumn('menu_items', 'preparation_time')) {
                $table->integer('preparation_time')->nullable()->after('spiciness_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['protein', 'carbs', 'fat', 'dietary_tags', 'spiciness_level', 'preparation_time']);
        });
    }
};
