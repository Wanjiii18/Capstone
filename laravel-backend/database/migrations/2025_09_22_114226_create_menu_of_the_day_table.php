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
        Schema::create('menu_of_the_day', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id'); 
            $table->unsignedBigInteger('inventory_id'); 
            $table->integer('servings'); 
            $table->date('date'); 
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_of_the_day');
    }
};
