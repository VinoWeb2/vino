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
        Schema::create('bouteilles', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->string('type', 100);
            $table->string('pays', 100);
            $table->year('millesime')->nullable();
            $table->text('cepage')->nullable();
            $table->decimal('taux_alcool', 2, 2)->nullable();
            $table->integer('format')->nullable();
            $table->decimal('prix', 4, 2)->nullable();
            $table->text('image')->nullable();
            $table->boolean('est_saq');
            $table->string('pastille_gout', 100)->nullable();
            $table->string('code_saq', 50)->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouteilles');
    }
};
