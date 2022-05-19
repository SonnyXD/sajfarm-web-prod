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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->string('cim_code')->nullable();
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
        Schema::dropIfExists('invoice_items');
    }
};
