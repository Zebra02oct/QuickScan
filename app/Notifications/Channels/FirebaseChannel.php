<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;

class FirebaseChannel
{
    // Parameter disesuaikan dengan standar Laravel: ($notifiable, $notification)
    public function send($notifiable, Notification $notification): void
    {
        // 1. Pastikan class notifikasi memiliki fungsi toFirebase
        if (!method_exists($notification, 'toFirebase')) {
            return;
        }

        // 2. Pastikan penerimanya adalah User dan memiliki fcm_token
        if (!$notifiable instanceof User || !$notifiable->fcm_token) {
            return;
        }

        // 3. Ambil array datanya
        $data = $notification->toFirebase($notifiable);

        // 4. Kirim menggunakan Service yang sudah Anda buat
        FirebaseService::sendNotification(
            $data['token'],
            $data['notification']['title'],
            $data['notification']['body'],
            $data['data'] ?? []
        );
    }
}