<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Drop all tables in correct order (dependent tables first)
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('karenderias');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Create karenderias table first (no dependencies)
        Schema::create('karenderias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->json('operating_days')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->string('business_permit')->nullable();
            $table->string('logo_url')->nullable();
            $table->json('images')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->integer('delivery_time_minutes')->default(30);
            $table->boolean('accepts_cash')->default(true);
            $table->boolean('accepts_online_payment')->default(false);
            $table->timestamps();
            
            $table->index(['owner_id', 'status']);
            $table->index(['latitude', 'longitude']);
        });

        // Create menu_items table (depends on karenderias)
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karenderia_id')->constrained('karenderias')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->string('category');
            $table->string('image_url')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('preparation_time_minutes')->default(15);
            $table->integer('calories')->nullable();
            $table->json('ingredients')->nullable();
            $table->json('allergens')->nullable();
            $table->text('dietary_info')->nullable();
            $table->integer('spice_level')->nullable();
            $table->integer('serving_size')->default(1);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_orders')->default(0);
            $table->timestamps();
            
            $table->index(['karenderia_id', 'category']);
            $table->index(['karenderia_id', 'is_available']);
        });

        // Create orders table (depends on karenderias and users)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('karenderia_id')->constrained('karenderias')->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cash', 'gcash', 'maya', 'card'])->default('cash');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('service_fee', 8, 2)->default(0);
            $table->decimal('tax', 8, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('delivery_address')->nullable();
            $table->json('delivery_coordinates')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->json('order_tracking')->nullable();
            $table->decimal('customer_rating', 3, 2)->nullable();
            $table->text('customer_review')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['karenderia_id', 'status']);
            $table->index(['created_at', 'karenderia_id']);
        });

        // Create order_items table (depends on orders and menu_items)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('unit_cost', 8, 2)->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('special_instructions')->nullable();
            $table->json('customizations')->nullable();
            $table->timestamps();
            
            $table->index(['order_id']);
            $table->index(['menu_item_id']);
        });

        // Create inventory table (depends on karenderias)
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karenderia_id')->constrained('karenderias')->onDelete('cascade');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('unit');
            $table->decimal('current_stock', 10, 3);
            $table->decimal('minimum_stock', 10, 3)->default(0);
            $table->decimal('maximum_stock', 10, 3)->nullable();
            $table->decimal('unit_cost', 8, 2);
            $table->decimal('total_value', 10, 2);
            $table->string('supplier')->nullable();
            $table->date('last_restocked')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['available', 'low_stock', 'out_of_stock', 'expired'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['karenderia_id', 'category']);
            $table->index(['karenderia_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Drop tables in reverse order
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('karenderias');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
