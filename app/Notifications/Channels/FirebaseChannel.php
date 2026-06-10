<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;

class FirebaseChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFirebase')) {
            return;
        }
        if (!$notifiable instanceof User || !$notifiable->fcm_token) {
            return;
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