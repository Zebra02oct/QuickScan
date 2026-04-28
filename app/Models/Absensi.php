<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absensi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'absensis'; 
    protected $guarded = ['id'];

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Jadwal Mapel
    public function guruMapel()
    {
        return $this->belongsTo(guruMapel::class); // Pastikan lo bikin Model Jadwal nanti
    }
}