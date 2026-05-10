<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithFileUploads;

    #[Layout('layouts.app')]
    #[Title('Profil Saya')]

   
    public $foto_baru;

  
    public $password_lama;
    public $password_baru;
    public $password_baru_confirmation;

    public function updateFoto()
    {
        $this->validate([
            'foto_baru' => 'required|image|max:2048', 
        ]);

        $user = Auth::user();

        if ($user->foto && Storage::disk('public')->exists($user->user_photo)) {
            Storage::disk('public')->delete($user->user_photo);
        }

        // Simpan foto baru
        $path = $this->foto_baru->store('profil', 'public');
        
        $user->update(['user_photo' => $path]);

        $this->reset('foto_baru');
        
        $this->dispatch('swal:success', title: 'Berhasil', text: 'Foto profil berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $this->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed', 
        ]);

        $user = Auth::user();

        if (!Hash::check($this->password_lama, $user->password)) {
            $this->addError('password_lama', 'Password lama tidak sesuai.');
            return;
        }

      
        $user->update([
            'password' => Hash::make($this->password_baru)
        ]);

        $this->reset(['password_lama', 'password_baru', 'password_baru_confirmation']);

        $this->dispatch('swal:success', title: 'Berhasil', text: 'Password berhasil diperbarui!');
    }

    public function render()
    {
        
        return view('livewire.profile.index', [
            'user' => Auth::user()
        ]);
    }
}