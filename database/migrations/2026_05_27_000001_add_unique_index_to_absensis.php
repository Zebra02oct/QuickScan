<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->unique(['sesi_absensi_id', 'siswa_id'], 'absensis_sesi_absensi_id_siswa_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensis_sesi_absensi_id_siswa_id_unique');
        });
    }
};
