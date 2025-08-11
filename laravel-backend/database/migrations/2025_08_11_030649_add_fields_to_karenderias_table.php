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
        Schema::table('karenderias', function (Blueprint $table) {
            // Add missing fields for karenderia owner registration
            $table->string('business_name')->after('name');
            $table->string('city')->nullable()->after('address');
            $table->string('province')->nullable()->after('city');
            $table->string('business_email')->nullable()->after('email');
            
            // Add approval tracking fields
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            
            // Update status enum to include 'approved' and 'rejected'
            $table->enum('status', ['pending', 'approved', 'active', 'inactive', 'rejected'])->default('pending')->change();
            
            // Add foreign key for approved_by
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karenderias', function (Blueprint $table) {
            // Remove foreign key first
            $table->dropForeign(['approved_by']);
            
            // Drop added columns
            $table->dropColumn([
                'business_name',
                'city', 
                'province',
                'business_email',
                'approved_at',
                'approved_by'
            ]);
            
            // Revert status enum
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending')->change();
        });
    }
};
