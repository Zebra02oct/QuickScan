<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapel extends Model
{
  protected $table = 'mapels';
    use SoftDeletes;

    // Daftar kolom yang boleh diisi secara massal
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kategori',
    ];
    public function guruMapel()
    {
        return $this->hasMany(guruMapel::class);
    }
}
