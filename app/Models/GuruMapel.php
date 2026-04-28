<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuruMapel extends Model
{
     use SoftDeletes;
protected $table = 'guru_mapels';
    protected $guarded = ['id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

   public function mapel()
    {
        return $this->belongsTo(Mapel::class)->withTrashed();
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
