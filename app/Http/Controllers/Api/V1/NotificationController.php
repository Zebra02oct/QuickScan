<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function registerToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update([
            'fcm_token' => $validated['fcm_token'],
        ]);

        return response()->json([
            'message' => 'FCM token berhasil disimpan.',
        ]);
    }

    public function removeToken(Request $request): JsonResponse
    {
        $request->user()->update([
            'fcm_token' => null,
        ]);

        return response()->json([
            'message' => 'FCM token berhasil dihapus.',
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min((int) $request->integer('per_page', 20), 100));

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $notifications->getCollection()->map(function ($notification) {
                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? $notification->type,
                    'state' => $data['state'] ?? null,
                    'title' => $data['title'] ?? 'Notifikasi',
                    'message' => $data['message'] ?? '',
                    'sesi_absensi_id' => $data['sesi_absensi_id'] ?? null,
                    'mapel' => $data['mapel'] ?? null,
                    'kelas' => $data['kelas'] ?? null,
                    'guru' => $data['guru'] ?? null,
                    'qr_token' => $data['qr_token'] ?? null,
                    'read_at' => $notification->read_at,
                    'created_at' => optional($notification->created_at)?->toDateTimeString(),
                ];
            })->values(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }
}
