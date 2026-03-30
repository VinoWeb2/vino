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
        Schema::table('consommations', function (Blueprint $table) {
            $table->dropForeign(['id_inventaire']);
            $table->dropColumn(['id_inventaire', 'quantite']);
            $table->unsignedBigInteger('id_utilisateur')->after('id');
            $table->unsignedBigInteger('id_bouteille')->after('id_utilisateur');
            $table->foreign('id_utilisateur')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('id_bouteille')->references('id')->on('bouteilles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consommations', function (Blueprint $table) {
            $table->dropForeign(['id_utilisateur']);
            $table->dropForeign(['id_bouteille']);
            $table->dropColumn(['id_utilisateur', 'id_bouteille']);
            $table->unsignedBigInteger('id_inventaire')->after('id');
            $table->integer('quantite')->after('id_inventaire');
            $table->foreign('id_inventaire')->references('id')->on('inventaires')->onDelete('cascade');
        });
    }
};
