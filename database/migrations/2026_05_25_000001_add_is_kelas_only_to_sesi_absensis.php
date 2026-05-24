<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key constraint lama
        Schema::table('sesi_absensis', function (Blueprint $table) {
            $table->dropForeign(['guru_mapel_id']);
        });

        // Modify guru_mapel_id menjadi nullable
        Schema::table('sesi_absensis', function (Blueprint $table) {
            $table->unsignedBigInteger('guru_mapel_id')->nullable()->change();
        });

        // Tambah kolom baru
        Schema::table('sesi_absensis', function (Blueprint $table) {
            $table->boolean('is_kelas_only')->default(false)->after('status');
            $table->unsignedBigInteger('wali_kelas_id')->nullable()->after('is_kelas_only');
            $table->unsignedBigInteger('kelas_id')->nullable()->after('wali_kelas_id');
        });
    }

    public function down(): void
    {
        Schema::table('sesi_absensis', function (Blueprint $table) {
            $table->dropColumn(['is_kelas_only', 'wali_kelas_id', 'kelas_id']);
            $table->unsignedBigInteger('guru_mapel_id')->nullable(false)->change();
        });
    }
};