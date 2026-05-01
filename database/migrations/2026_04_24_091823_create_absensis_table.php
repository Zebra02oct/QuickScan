<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
          $table->id();
            $table->foreignId('sesi_absensi_id')->constrained('sesi_absensis')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
         
            $table->time('waktu_scan')->nullable()->comment('Waktu tepat saat siswa berhasil scan');
         
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa', 'terlambat'])->default('alpa');
    
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};