<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumption_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumption_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->foreignId('item_stock_id')->constrained();
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumption_items');
    }
};
