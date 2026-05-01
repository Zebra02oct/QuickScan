<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SesiAbsensi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sesi_absensis';


    protected $guarded = ['id'];
    
    protected $casts = [
        'tanggal' => 'date',
    ];

   
    public function guruMapel()
    {
        return $this->belongsTo(GuruMapel::class)->withTrashed();
    }

 
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'sesi_absensi_id');
    }

    public static function tutupSesiOtomatis()
    {
        $sesiLama = self::where('status', 'berjalan')
            ->where('created_at', '<=', now()->subHours(3))
            ->get();


        foreach ($sesiLama as $sesi) {
        
            $siswaSudahAbsen = \App\Models\Absensi::where('sesi_absensi_id', $sesi->id)
                ->pluck('siswa_id')
                ->toArray();

            $semuaSiswaKelas = $sesi->guruMapel->kelas->siswas->pluck('id')->toArray();

            $siswaAlpa = array_diff($semuaSiswaKelas, $siswaSudahAbsen);

            $sesi->update([
                'status' => 'selesai',
                'waktu_selesai' => $sesi->created_at->addHours(3)->toTimeString(),
                'token_qr' => null
            ]);

            $dataInsert = [];
            foreach ($siswaAlpa as $idSiswa) {
                $dataInsert[] = [
                    'sesi_absensi_id' => $sesi->id,
                    'siswa_id' => $idSiswa,
                    'status' => 'alpa',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($dataInsert)) {
                \App\Models\Absensi::insert($dataInsert);
            }
        }
    }
}