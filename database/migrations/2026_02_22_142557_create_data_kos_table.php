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
        Schema::create('data_kos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kos_id')->unique();
            $table->unsignedBigInteger('pemilik_id');
            $table->string('nama_pemilik');
            $table->string('no_wa_pemilik')->nullable();
            $table->string('nama_kos');
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kos');
    }
};
