<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Request;

#[Layout('layouts.guest')]
#[Title('Atur Ulang Password')]
class ResetPassword extends Component
{
    public string $token;
    public string $email = '';
    public bool $isExpired = false;

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = Request::query('email', '');

        $user = User::where('email', $this->email)->first();

        if (!$user || !Password::broker()->tokenExists($user, $this->token)) {
            $this->isExpired = true;
        }
    }

    public function resetPassword(): void
    {
        if ($this->isExpired) {
            return;
        }

        $this->validate();

        $status = Password::broker()->reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Password berhasil diubah! Silakan login.');
            $this->redirectRoute('login', navigate: true);
        } else {
            throw ValidationException::withMessages([
                'email' => [match ($status) {
                    Password::INVALID_TOKEN => 'Tautan reset password tidak valid atau sudah kedaluwarsa.',
                    Password::INVALID_USER => 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.',
                    default => 'Terjadi kesalahan, silakan coba lagi.',
                }],
            ]);
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}