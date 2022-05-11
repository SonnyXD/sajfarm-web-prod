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
        Schema::create('aviz_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aviz_entry_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->string('cim_code');
            $table->string('product_code');
            $table->integer('quantity');
            $table->date('exp_date');
            $table->string('lot');
            $table->foreignId('measure_unit_id')->constrained();
            $table->float('price');
            $table->integer('tva');
            $table->float('tva_price');
            $table->float('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aviz_entry_items');
    }
};
