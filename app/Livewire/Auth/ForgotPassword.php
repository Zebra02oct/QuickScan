<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Lupa Password')]
class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public ?string $status = null;

    public function sendPasswordResetLink(): void
    {
        $this->validate();

        $status = Password::broker()->sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = 'Tautan reset password telah dikirim ke email Anda.';
            $this->email = '';
        } else {
            throw ValidationException::withMessages([
                'email' => 'Kami tidak dapat menemukan pengguna dengan email tersebut.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}