<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tingkat',
        'jurusan',
        'nama_kelas',
        'guru_id', 
    ];

    /**
     * Relasi ke Guru (Sebagai Wali Kelas)
     */
    public function waliKelas(): BelongsTo
    {
        // Kita spesifikasikan 'guru_id' sebagai foreign key-nya
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke daftar siswa yang ada di kelas ini sekarang
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * Relasi ke jadwal pelajaran di kelas ini
     */
public function guruMapel()
{
    return $this->hasMany(guruMapel::class);
}
}