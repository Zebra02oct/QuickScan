<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $sesi_absensi_id
 * @property int $siswa_id
 * @property string|null $waktu_scan Waktu tepat saat siswa berhasil scan
 * @property string $status
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\GuruMapel|null $guruMapel
 * @property-read \App\Models\Siswa|null $siswa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereSesiAbsensiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereSiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereWaktuScan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi withoutTrashed()
 */
	class Absensi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $nip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Absensi> $absensis
 * @property-read int|null $absensis_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GuruMapel> $guruMapels
 * @property-read int|null $guru_mapels_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru withoutTrashed()
 */
	class Guru extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $kelas_id
 * @property int $mapel_id
 * @property int $guru_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Absensi> $absensis
 * @property-read int|null $absensis_count
 * @property-read \App\Models\Guru|null $guru
 * @property-read \App\Models\Kelas|null $kelas
 * @property-read \App\Models\Mapel|null $mapel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel withoutTrashed()
 */
	class GuruMapel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $guru_id
 * @property string $tingkat
 * @property string $jurusan
 * @property string $nama_kelas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GuruMapel> $guruMapel
 * @property-read int|null $guru_mapel_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Siswa> $siswas
 * @property-read int|null $siswas_count
 * @property-read \App\Models\Guru|null $waliKelas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereNamaKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereTingkat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas withoutTrashed()
 */
	class Kelas extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $kode_mapel
 * @property string $nama_mapel
 * @property string $kategori
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GuruMapel> $guruMapel
 * @property-read int|null $guru_mapel_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereKodeMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereNamaMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel withoutTrashed()
 */
	class Mapel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $guru_mapel_id
 * @property string $tanggal
 * @property string $waktu_mulai
 * @property string|null $waktu_selesai Terisi saat guru klik Tutup Sesi
 * @property string|null $token_qr Token acak untuk QR Code
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereGuruMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereTokenQr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereWaktuMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiAbsensi whereWaktuSelesai($value)
 */
	class SesiAbsensi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $kelas_id
 * @property string $nisn
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Absensi> $absensis
 * @property-read int|null $absensis_count
 * @property-read \App\Models\Kelas|null $kelas
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNisn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa withoutTrashed()
 */
	class Siswa extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $role
 * @property string $status
 * @property string|null $jenis_kelamin
 * @property string|null $user_photo
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $avatar_url
 * @property-read \App\Models\Guru|null $guru
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GuruMapel> $jadwals_guru
 * @property-read int|null $jadwals_guru_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Siswa|null $siswa
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

