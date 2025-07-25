<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKarenderiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karenderias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->json('operating_days')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->integer('delivery_time_minutes')->default(30);
            $table->boolean('accepts_cash')->default(true);
            $table->boolean('accepts_online_payment')->default(false);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->unsignedBigInteger('owner_id');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('karenderias');
    }
}
