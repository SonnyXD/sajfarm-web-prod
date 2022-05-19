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
        Schema::create('consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->default('2')->constrained();
            $table->foreignId('medic_id')->default(0)->nullable()->constrained();
            $table->foreignId('ambulance_id')->nullable()->constrained();
            $table->integer('patient_number')->nullable();
            $table->string('tour');
            $table->date('document_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumptions');
    }
};
