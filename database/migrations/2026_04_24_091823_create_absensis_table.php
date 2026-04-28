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
            
            // Relasi Utama
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            
            $table->foreignId('guru_mapel_id')->constrained('guru_mapels')->cascadeOnDelete();
            
            // Data Kehadiran
            $table->date('tanggal');
            $table->time('waktu')->nullable()->comment('Waktu tepat saat siswa scan QR');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa','terlambar']);
            
    
            $table->string('lokasi')->nullable()->comment('Format: lat,long');
            
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