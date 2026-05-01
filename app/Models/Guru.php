<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nip',
        'jenis_kelamin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

   public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class);
    }

 
  
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}