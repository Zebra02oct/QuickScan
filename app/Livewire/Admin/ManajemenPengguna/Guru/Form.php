<?php

namespace App\Livewire\Admin\ManajemenPengguna\Guru;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public $guru_id = null;
      public $user_id = null; 
      public $status = 'aktif'; 

      public $nama;
      public $nip;
      public $jenis_kelamin;
      public $email;
      public $password;

        protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'nip' => [
                'required',
                Rule::unique('gurus', 'nip')->ignore($this->guru_id),
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'password' => 'nullable|min:6',
            'status' => 'required|in:aktif,pindah,nonaktif',
        ];
    }

    protected $messages = [
        'nama.required'    => 'Nama wajib diisi.',
        'nip.required'    => 'NIP wajib diisi.',
        'jenis_kelamin.required'    => 'Jenis Kelamin wajib diisi.',
        'email.required'    => 'Email wajib diisi.',
        'status.required'    => 'Status wajib diisi.',
        'nip.unique'      => 'Nip ini sudah terdaftar di sistem.',
        'email.unique'     => 'Email ini sudah dipakai oleh pengguna lain.',
        'password.min'     => 'Password minimal 6 karakter.',
    ];

    public function loadData($id = null){
 $this->reset();
        $this->resetValidation();
        $this->status = 'aktif';
     if ($id) {
            $data = Guru::with('user')->find($id);
            
            if ($data) { 
                $this->guru_id = $data->id;
                $this->user_id = $data->user_id;
                $this->nama = $data->user?->name;
                $this->nip = $data->nip;
                $this->email = $data->user?->email;
                $this->status = $data->user?->status;
                $this->jenis_kelamin = $data->user?->jenis_kelamin;
            }
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->guru_id) {
              
                $guru = Guru::findOrFail($this->guru_id);
                $user = User::findOrFail($guru->user_id);

             
                $userData = [
                    'name'   => $this->nama,
                    'email'  => $this->email,
                    'status' => $this->status,
                     'jenis_kelamin' => $this->jenis_kelamin,
                ];

             
                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }

                $user->update($userData);

            
                $guru->update([
                    'nip'          => $this->nip,
                ]);

                $message = 'Data guru berhasil diperbarui.';

            } else {
            
                $passwordFix = !empty($this->password) ? $this->password : $this->nip;

           
                $user = User::create([
                    'name'     => $this->nama,
                    'email'    => $this->email,
                    'status'   => $this->status,
                     'jenis_kelamin' => $this->jenis_kelamin,
                    'password' => Hash::make($passwordFix),
                    'role' => 'guru',
                ]);

               
                Guru::create([
                    'user_id'       => $user->id,
                    'nip'          => $this->nip,
                   
                ]);

                $message = 'Data Guru berhasil ditambahkan.';
            }

            DB::commit();

            $this->dispatch('close-modal');
            $this->dispatch('refresh-guru'); 
            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('swal:error', [
                'title' => 'Gagal Menyimpan!',
                'text'  => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.manajemen-pengguna.guru.form');
    }
}
