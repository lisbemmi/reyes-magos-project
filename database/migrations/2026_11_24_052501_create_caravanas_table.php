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
        Schema::create('caravanas', function (Blueprint $table) {
            $table->id();
            $table->string('rey')->unique();       // una caravana por rey
            $table->string('ofrenda')->unique();   // ofrenda no se repite
            $table->string('origen');
            $table->unsignedTinyInteger('camellos');
            $table->string('estado')->default('DETENIDA');
            $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caravanas');
    }
};
