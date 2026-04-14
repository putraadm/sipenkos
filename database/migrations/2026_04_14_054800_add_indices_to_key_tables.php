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
        Schema::table('riwayat_mutasi', function (Blueprint $table) {
            $table->index('tanggal_mutasi');
            $table->index('jenis_mutasi');
            $table->index(['kos_id', 'penghuni_id']);
        });

        Schema::table('data_kos', function (Blueprint $table) {
            $table->index('pemilik_id');
            $table->index('nama_kos');
        });

        Schema::table('data_penghuni', function (Blueprint $table) {
            $table->index('nama');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_mutasi', function (Blueprint $table) {
            $table->dropIndex(['tanggal_mutasi']);
            $table->dropIndex(['jenis_mutasi']);
            $table->dropIndex(['kos_id', 'penghuni_id']);
        });

        Schema::table('data_kos', function (Blueprint $table) {
            $table->dropIndex(['pemilik_id']);
            $table->dropIndex(['nama_kos']);
        });

        Schema::table('data_penghuni', function (Blueprint $table) {
            $table->dropIndex(['nama']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
        });
    }
};
