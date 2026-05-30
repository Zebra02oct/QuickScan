<?php

namespace App\Models;

use App\Notifications\SesiAbsensiNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Notification;

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

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'sesi_absensi_id');
    }

    public function notifyAssignedStudents(string $state): void
    {
        $this->loadMissing('guruMapel.kelas.siswas.user');

        $users = $this->guruMapel?->kelas?->siswas?->map(fn ($siswa) => $siswa->user)->filter();

        if ($users && $users->isNotEmpty()) {
            Notification::send($users, new SesiAbsensiNotification($state, $this));
        }
    }

    public static function tutupSesiOtomatis()
    {
        $sesiLama = self::where('status', '=', 'berjalan')
            ->whereNotNull('guru_mapel_id')
            ->where('created_at', '<=', now()->subHours(3))
            ->with(['guruMapel.kelas.siswas'])
            ->get();

        foreach ($sesiLama as $sesi) {
            $siswaSudahAbsen = Absensi::where('sesi_absensi_id', '=', $sesi->id)
                ->pluck('siswa_id')
                ->toArray();

            $semuaSiswaKelas = $sesi->guruMapel?->kelas?->siswas?->pluck('id')->toArray() ?? [];

            $siswaAlpa = array_diff($semuaSiswaKelas, $siswaSudahAbsen);

            $sesi->update([
                'status' => 'selesai',
                'waktu_selesai' => $sesi->created_at->addHours(3)->toTimeString(),
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

            $sesi->notifyAssignedStudents('ditutup');
        }
    }
}