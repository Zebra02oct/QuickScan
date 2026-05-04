<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{

    protected $fillable = [
        'tingkat',
        'jurusan',
        'nama_kelas',
        'is_active',
        'guru_id', 
    ];

   
    public function waliKelas(): BelongsTo
    {
     
        return $this->belongsTo(Guru::class, 'guru_id');
    }

 
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

 
public function guruMapel()
{
    return $this->hasMany(guruMapel::class);
}
}