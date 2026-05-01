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
        Schema::create('sesi_absensis', function (Blueprint $table) {
        $table->id();
            
            $table->foreignId('guru_mapel_id')->constrained('guru_mapels')->cascadeOnDelete();
            
            
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable()->comment('Terisi saat guru klik Tutup Sesi');
            
        
            $table->string('token_qr')->nullable()->comment('Token acak untuk QR Code');
            $table->enum('status', ['berjalan', 'selesai'])->default('berjalan');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_absensis');
    }
};
