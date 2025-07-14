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
        Schema::create('karenderias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->boolean('is_open')->default(true);
            $table->json('opening_hours')->nullable();
            $table->string('image_url')->nullable();
            $table->json('cuisine_type')->nullable();
            $table->json('price_range')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karenderias');
    }
};
