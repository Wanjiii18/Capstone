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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer');
            $table->boolean('verified')->default(false);
            $table->string('username')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('application_status')->nullable();
            $table->string('photo_url')->nullable();
            $table->integer('age')->nullable();
            $table->decimal('height', 5, 2)->nullable(); // in cm
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->string('activity_level')->nullable();
            $table->string('fitness_goal')->nullable();
            $table->json('allergies')->nullable();
            $table->json('dietary_restrictions')->nullable();
            $table->json('cuisine_preferences')->nullable();
            $table->json('preferred_meal_times')->nullable();
            $table->json('location')->nullable();
            $table->json('preferences')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'verified', 'username', 'phone_number', 'address',
                'application_status', 'photo_url', 'age', 'height', 'weight',
                'activity_level', 'fitness_goal', 'allergies', 'dietary_restrictions',
                'cuisine_preferences', 'preferred_meal_times', 'location', 'preferences'
            ]);
        });
    }
};
