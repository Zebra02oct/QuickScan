<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use App\Models\User;
use Kreait\Firebase\Factory;

class FirebaseService
{
    private static ?object $messaging = null;

    public static function getMessaging(): object
    {
        if (self::$messaging === null) {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials.auto'));
            self::$messaging = $factory->createMessaging();
        }

        return self::$messaging;
    }

    public static function sendNotification(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            // Gunakan class CloudMessage bawaan Kreait
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($data)
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'sesi_absensi',
                        'sound' => 'default',
                    ],
                ]));

            self::getMessaging()->send($message);
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Firebase Send Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendMulticast(array $tokens, string $title, string $body, array $data = []): array
    {
        if (empty($tokens)) {
            return ['success' => 0, 'failure' => 0];
        }

        try {
            $message = [
                'tokens' => $tokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'sesi_absensi',
                        'sound' => 'default',
                    ],
                ],
            ];

            $sendReport = self::getMessaging()->sendAll([$message]);

            return [
                'success' => $sendReport->successes()->count(),
                'failure' => $sendReport->failures()->count(),
            ];
        } catch (\Exception $e) {
            report($e);
            return ['success' => 0, 'failure' => count($tokens)];
        }
    }
}