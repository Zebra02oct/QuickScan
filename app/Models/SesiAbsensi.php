<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesiAbsensi extends Model
{
    use HasFactory;

    protected $table = 'sesi_absensis';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function guruMapel(): BelongsTo
    {
        return $this->belongsTo(GuruMapel::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'sesi_absensi_id');
    }

    public static function tutupSesiOtomatis()
    {
        $sesiLama = self::where('status', 'berjalan')
            ->where('created_at', '<=', now()->subHours(3))
            ->with('guruMapel.kelas.siswas')
            ->get();

        foreach ($sesiLama as $sesi) {
            $siswaSudahAbsen = Absensi::where('sesi_absensi_id', $sesi->id)
                ->pluck('siswa_id')
                ->toArray();

            $semuaSiswaKelas = $sesi->guruMapel?->kelas?->siswas?->pluck('id')->toArray() ?? [];

            $siswaAlpa = array_diff($semuaSiswaKelas, $siswaSudahAbsen);

            $sesi->update([
                'status' => 'selesai',
                'waktu_selesai' => $sesi->created_at->copy()->addHours(3)->toTimeString(),
                'token_qr' => null,
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
                Absensi::insert($dataInsert);
            }
        }
    }
}
