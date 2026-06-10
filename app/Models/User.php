<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'jenis_kelamin',
        'user_photo',
        'fcm_token',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }
    public function siswa()
    {
        return $this->hasOne(siswa::class);
    }

    public function jadwals_guru()
    {
        return $this->hasMany(GuruMapel::class, 'guru_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'superadmin';
    }
    public function isGuru()
    {
        return $this->role === 'guru';
    }
    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    public function getAvatarUrlAttribute(): string
    {

        return 'https://ui-avatars.com/api/?name='
            . urlencode($this->name ?: 'User')
            . '&size=64&background=E0E7FF&color=3730A3&format=svg';
    }
}