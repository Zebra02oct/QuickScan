<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $email = $request->email;
        $password = $request->password;

        $user = User::query()
            ->where('email', $email)
            ->orWhereHas('siswa', function ($query) use ($email) {
                $query->where('nisn', $email);
            })
            ->first();

        if (! $user) {
            return response()->json([
                'message' => 'NISN/Email tidak terdaftar.',
            ], 404);
        }

        if (! Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Password salah.',
            ], 401);
        }

        if ($user->status !== 'aktif') {
            return response()->json([
                'message' => 'Akun Anda tidak aktif. Hubungi admin sekolah.',
            ], 403);
        }

        $deviceName = $request->device_name ?? 'flutter-client';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'siswa_id' => $user->siswa?->id,
                'kelas_id' => $user->siswa?->kelas_id,
                'guru_id' => $user->guru?->id,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing(['siswa.kelas', 'guru']);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'siswa_id' => $user->siswa?->id,
                'kelas_id' => $user->siswa?->kelas_id,
                'guru_id' => $user->guru?->id,
            ],
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama yang Anda masukkan salah.',
            ], 400);
        }
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ]);
    }
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $request->user()->tokens()->where('id', $token->id)->delete();
        }

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing(['siswa.kelas', 'guru']);

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'avatar' => $user->avatar ?? null,
        ];

        if ($user->role === 'siswa' && $user->siswa) {
            $response['siswa'] = [
                'id' => $user->siswa->id,
                'nisn' => $user->siswa->nisn,
                'phone' => $user->siswa->phone ?? '-',
                'address' => $user->siswa->address ?? '-',
                'gender' => $user->siswa->gender ?? '-',
                'birth_date' => $user->siswa->birth_date ?? '-',
                'kelas' => [
                    'id' => $user->siswa->kelas_id,
                    'nama_kelas' => $user->siswa->kelas?->nama_kelas ?? '-',
                    'tingkat' => $user->siswa->kelas?->tingkat ?? '-',
                    'jurusan' => $user->siswa->kelas?->jurusan ?? '-',
                ],
            ];
        }

        if ($user->role === 'guru' && $user->guru) {
            $response['guru'] = [
                'id' => $user->guru->id,
                'nip' => $user->guru->nip ?? '-',
                'phone' => $user->guru->phone ?? '-',
                'address' => $user->guru->address ?? '-',
                'gender' => $user->guru->gender ?? '-',
                'birth_date' => $user->guru->birth_date ?? '-',
            ];
        }

        return response()->json([
            'profile' => $response,
        ]);
    }
}