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
        // Check if table already exists to prevent duplicate creation
        if (!Schema::hasTable('karenderias')) {
            Schema::create('karenderias', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('owner_name');
                $table->string('address');
                $table->timestamps();
            });
        }
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