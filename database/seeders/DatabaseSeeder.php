<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
      
        User::create([
            'name' => 'Super Admin SMK',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'status' => 'aktif',
        ]);

       
        User::create([
            'name' => 'Admin Absensi',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

       
        $dataGuru = [
            [
                'name' => 'Budi Santoso, S.Kom',
                'email' => 'guru1@gmail.com',
                'nip' => '19850101',
                'jk' => 'L'
            ],
            [
                'name' => 'Siti Aminah, M.Pd',
                 'email' => 'guru2@gmail.com',
                'nip' => '19900202',
                'jk' => 'P'
            ],
        ];

        foreach ($dataGuru as $g) {
            $user = User::create([
                'name' => $g['name'],
                 'email' => $g['email'],
                'password' => Hash::make('password'),
                'role' => 'guru',
                'jenis_kelamin' => $g['jk'],
                'status' => 'aktif',
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => $g['nip'],
            ]);
        }

      
        $dataSiswa = [
            [
                'name' => 'Ahmad Fauzi',
                 'email' => 'siswa1@gmail.com',
                'nisn' => '2021001',
                'jk' => 'L'
            ],
            [
                'name' => 'Lani Cahyani',
                'email' => 'siswa2@gmail.com',
                'nisn' => '2021002',
                'jk' => 'P'
            ],
        ];

        foreach ($dataSiswa as $s) {
            $user = User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'jenis_kelamin' => $s['jk'],
                'status' => 'aktif',
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'nisn' => $s['nisn'],
              
            ]);
        }
    }
}