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
        Schema::create('liste_achats', function (Blueprint $table) {
            $table->id();

            $table->string('nom', 75);
            $table->text('description');

            $table->unsignedBigInteger('id_utilisateur');
            $table->foreign('id_utilisateur')->references('id')->on('utilisateurs')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liste_achats');
    }
};
