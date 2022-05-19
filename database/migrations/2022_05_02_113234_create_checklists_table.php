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
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained();
            $table->foreignId('medic_id')->default(0)->nullable()->constrained();
            $table->foreignId('assistent_id')->default(0)->nullable()->constrained();
            $table->foreignId('ambulancier_id')->default(0)->nullable()->constrained();
            $table->foreignId('ambulance_id')->nullable()->constrained();
            $table->date('checklist_date');
            $table->integer('patient_number')->nullable();
            $table->string('tour');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklists');
    }
};
