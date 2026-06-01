<?php

namespace App\Notifications;

use App\Models\SesiAbsensi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SesiAbsensiNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $state,
        private readonly SesiAbsensi $sesi,
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];

        // Panggil kurir FirebaseChannel yang sudah Anda buat
        if ($notifiable->fcm_token) {
            $channels[] = \App\Notifications\Channels\FirebaseChannel::class;
        }

        return $channels;
    }

    public function toDatabase($notifiable): array
    {
        $sesi = $this->sesi->loadMissing(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user']);

        return [
            'type' => 'sesi_absensi',
            'state' => $this->state,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'sesi_absensi_id' => $sesi->id,
            'qr_token' => $sesi->token_qr,
            'tanggal' => optional($sesi->tanggal)->toDateString(),
            'waktu_mulai' => $sesi->waktu_mulai,
            'waktu_selesai' => $sesi->waktu_selesai,
            'mapel' => $sesi->guruMapel?->mapel?->nama_mapel,
            'kelas' => $sesi->guruMapel?->kelas?->nama_kelas,
            'guru' => $sesi->guruMapel?->guru?->user?->name,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    public function toFirebase($notifiable): array
    {
        $sesi = $this->sesi->loadMissing(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user']);

        return [
            'token' => $notifiable->fcm_token,
            'notification' => [
                'title' => $this->getTitle(),
                'body' => $this->getMessage(),
            ],
            'data' => [
                'type' => 'sesi_absensi',
                'state' => $this->state,
                'sesi_absensi_id' => (string) $sesi->id,
                'qr_token' => $sesi->token_qr,
                'tanggal' => optional($sesi->tanggal)->toDateString(),
                'waktu_mulai' => $sesi->waktu_mulai,
                'waktu_selesai' => $sesi->waktu_selesai,
                'mapel' => $sesi->guruMapel?->mapel?->nama_mapel ?? '',
                'kelas' => $sesi->guruMapel?->kelas?->nama_kelas ?? '',
                'guru' => $sesi->guruMapel?->guru?->user?->name ?? '',
            ],
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'channel_id' => 'sesi_absensi',
                    'sound' => 'default',
                ],
            ],
        ];
    }

    private function getTitle(): string
    {
        return match ($this->state) {
            'berlangsung' => 'Sesi Absensi Berlangsung',
            'ditutup' => 'Sesi Absensi Ditutup',
            'dibatalkan' => 'Sesi Absensi Dibatalkan',
            default => 'Pembaruan Sesi Absensi',
        };
    }

    private function getMessage(): string
    {
        return match ($this->state) {
            'berlangsung' => 'Sesi mata pelajaran sekarang sedang berlangsung dan dapat diakses untuk scan QR.',
            'ditutup' => 'Sesi mata pelajaran telah ditutup. Scan QR sudah tidak bisa digunakan.',
            'dibatalkan' => 'Sesi mata pelajaran dibatalkan oleh guru.',
            default => 'Ada pembaruan pada sesi mata pelajaran.',
        };
    }
}