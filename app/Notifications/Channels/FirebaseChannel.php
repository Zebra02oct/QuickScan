<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;

class FirebaseChannel
{
    public function send(Notification $notification, array $notifiables): void
    {
        foreach ($notifiables as $notifiable) {
            if (!method_exists($notification, 'toFirebase')) {
                continue;
            }

            if (!$notifiable instanceof User || !$notifiable->fcm_token) {
                continue;
            }

            $data = $notification->toFirebase($notifiable);

            FirebaseService::sendNotification(
                $data['token'],
                $data['notification']['title'],
                $data['notification']['body'],
                $data['data'] ?? []
            );
        }
    }
}