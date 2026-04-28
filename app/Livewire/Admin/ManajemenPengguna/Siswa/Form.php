<?php

namespace App\Livewire\Admin\ManajemenPengguna\Siswa;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Form extends Component
{
    public $siswa_id = null;
    public $user_id = null; 
    
    public $nama;
    public $nisn;
    public $jenis_kelamin;
    public $email;
    public $password; 
    public $status = 'aktif'; 
    public $kelas;

    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'nisn' => [
                'required',
                Rule::unique('siswas', 'nisn')->ignore($this->siswa_id),
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'password' => 'nullable|min:6',
            'kelas' => 'required|exists:kelas,id',
            'status' => 'required|in:aktif,lulus,pindah,nonaktif',
        ];
    }

    protected $messages = [
        'nama.required'    => 'Nama wajib diisi.',
        'nisn.required'    => 'NISN wajib diisi.',
        'jenis_kelamin.required'    => 'Jenis Kelamin wajib diisi.',
        'email.required'    => 'Email wajib diisi.',
        'status.required'    => 'Status wajib diisi.',
        'nisn.unique'      => 'NISN ini sudah terdaftar di sistem.',
        'email.unique'     => 'Email ini sudah dipakai oleh pengguna lain.',
        'password.min'     => 'Password minimal 6 karakter.',
        'kelas.required'   => 'Kelas wajib dipilih.',
    ];

  
    #[Computed]
    public function listKelas()
    {
        return Kelas::select('id', 'nama_kelas')->orderBy('tingkat')->orderBy('nama_kelas')->get();
    }

    public function loadData($id = null)
    {
        $this->reset();
        $this->resetValidation();
        $this->status = 'aktif';

        if ($id) {
            $data = Siswa::with('user', 'kelas')->find($id);

            if ($data) {
                $this->siswa_id      = $data->id;
                $this->user_id       = $data->user_id; 
                
                $this->nama          = $data->user?->name;
                $this->email         = $data->user?->email;
                $this->status        = $data->user?->status; 
                
                $this->nisn          = $data->nisn;
                $this->jenis_kelamin = $data->user->jenis_kelamin; 
                
                $this->kelas         = $data->kelas_id;
            }
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->siswa_id) {
              
                $siswa = Siswa::findOrFail($this->siswa_id);
                $user = User::findOrFail($siswa->user_id);

             
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

            
                $siswa->update([
                    'nisn'          => $this->nisn,
                    'kelas_id'      => $this->kelas,
                ]);

                $message = 'Data siswa berhasil diperbarui.';

            } else {
            
                $passwordFix = !empty($this->password) ? $this->password : $this->nisn;

           
                $user = User::create([
                    'name'     => $this->nama,
                    'email'    => $this->email,
                    'status'   => $this->status,
                     'jenis_kelamin' => $this->jenis_kelamin,
                    'password' => Hash::make($passwordFix),
                    'role' => 'siswa'
                ]);

               
                Siswa::create([
                    'user_id'       => $user->id,
                    'kelas_id'      => $this->kelas,
                    'nisn'          => $this->nisn,
                   
                ]);

                $message = 'Siswa baru berhasil ditambahkan.';
            }

            DB::commit();

            $this->dispatch('close-modal');
            $this->dispatch('refresh-siswa'); 
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
        return view('livewire.admin.manajemen-pengguna.siswa.form');
    }
}