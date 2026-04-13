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
        Schema::create('liste_achat_bouteille', function (Blueprint $table) {

            $table->unsignedBigInteger('id_liste_achat');
            $table->foreign('id_liste_achat')->references('id')->on('liste_achats')->onDelete('cascade');

            $table->unsignedBigInteger('id_bouteille');
            $table->foreign('id_bouteille')->references('id')->on('bouteilles')->onDelete('cascade');

            $table->primary(['id_liste_achat', 'id_bouteille']);

            $table->integer('quantite')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liste_achat_bouteille');
    }
};
