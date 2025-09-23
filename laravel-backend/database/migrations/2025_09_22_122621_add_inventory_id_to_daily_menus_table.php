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
        Schema::table('daily_menus', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->after('menu_item_id')->constrained('inventory')->onDelete('set null');
            $table->decimal('ingredient_quantity', 8, 3)->nullable()->after('inventory_id')->comment('Quantity of ingredient needed per serving');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_menus', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn(['inventory_id', 'ingredient_quantity']);
        });
    }
};
