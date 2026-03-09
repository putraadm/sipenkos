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
        Schema::create('rekap_pendapatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kos_id');
            $table->unsignedBigInteger('pemilik_id');
            $table->string('nama_kos'); 
            $table->string('nama_penghuni')->nullable();
            $table->string('nomor_kamar')->nullable();
            $table->string('tipe_kamar')->nullable();
            $table->date('periode_tagihan')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->decimal('nominal', 12, 2);
            $table->dateTime('tanggal_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_pendapatan');
    }
};
