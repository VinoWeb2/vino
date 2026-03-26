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
        Schema::create('celliers', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 75);
            $table->unsignedBigInteger('id_utilisateur');
            $table->text('description');
            $table->string('emplacement', 55);
            $table->foreign('id_utilisateur')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celliers');
    }
};
