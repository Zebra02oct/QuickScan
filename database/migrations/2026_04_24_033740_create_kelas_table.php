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
     Schema::create('kelas', function (Blueprint $table) {
        $table->id();
       $table->foreignId('guru_id')
                  ->nullable()
                  ->constrained('gurus')
                  ->nullOnDelete();

 $table->string('tingkat', 10); 
            $table->string('jurusan', 50); 
            $table->string('nama_kelas')->unique();
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
