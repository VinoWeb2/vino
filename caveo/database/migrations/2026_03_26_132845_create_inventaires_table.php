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
        Schema::create('inventaires', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('id_cellier');
            $table->unsignedBigInteger('id_bouteille');
            $table->integer('quantite');
            $table->text('description')->nullable();
            $table->timestamp('date_ajout')->useCurrent();
            $table->foreign('id_cellier')->references('id')->on('celliers')->onDelete('cascade');
            $table->foreign('id_bouteille')->references('id')->on('bouteilles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaires');
    }
};
