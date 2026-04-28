<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Import Siswa Baru</h2>
        <p class="text-sm text-slate-500">Tambahkan data siswa secara massal menggunakan file Excel.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl p-6">
                <form wire:submit.prevent="importData">

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-indigo-900 mb-1.5">
                            1. Pilih Kelas Tujuan <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="kelas_id"
                            class="w-full rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 bg-white">
                            <option value="">Pilih Kelas</option>
                            @foreach ($list_kelas as $kls)
                                <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-indigo-900 mb-1.5">
                            2. Unggah File Excel (.xlsx / .xls) <span class="text-rose-500">*</span>
                        </label>

                        <label for="file-upload"
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors cursor-pointer relative group">

                            <div class="space-y-1 text-center">
                                <i
                                    class="ri-file-excel-2-line text-4xl text-emerald-500 group-hover:scale-110 transition-transform"></i>

                                <div class="flex text-sm text-gray-600 justify-center mt-2">
                                    <span class="font-medium text-indigo-600 group-hover:text-indigo-700">
                                        Pilih file excel
                                    </span>
                                    <p class="pl-1">atau seret ke sini</p>

                                    <input id="file-upload" type="file" wire:model.live="file_excel"
                                        accept=".xlsx, .xls" class="sr-only">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Maksimal 5MB</p>
                            </div>
                        </label>

                        @if ($file_excel)
                            <div
                                class="mt-3 flex items-center justify-between p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-sm animate-fade-in-up">
                                <div class="flex items-center gap-2 text-emerald-700 font-medium overflow-hidden">
                                    <i class="ri-file-excel-fill text-lg flex-shrink-0"></i>
                                    <span class="truncate max-w-[200px] sm:max-w-xs"
                                        title="{{ $file_excel->getClientOriginalName() }}">
                                        {{ $file_excel->getClientOriginalName() }}
                                    </span>
                                </div>

                                <button type="button" wire:click="$set('file_excel', null)"
                                    class="text-emerald-600 hover:text-rose-600 hover:bg-rose-100 p-1.5 rounded-lg transition-colors flex-shrink-0"
                                    title="Hapus file">
                                    <i class="ri-close-line text-lg font-bold"></i>
                                </button>
                            </div>
                        @endif

                        @error('file_excel')
                            <span class="text-rose-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="border-t  border-gray-100 pt-5 flex justify-end gap-3">
                        <x-ui.button size="sm" type="button" href="{{ route('admin.siswa.index') }}"
                            color="white" wire:navigate>
                            Kembali
                        </x-ui.button>

                        <x-ui.button size="sm" type="submit" color="primary" icon="ri-upload-cloud-2-line"
                            wire:loading.attr="disabled" wire:target="importData, file_excel">
                            Mulai Import Data
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold">
                        <i class="ri-information-line text-xl"></i>
                    </div>
                    <h3 class="font-bold text-indigo-900">Panduan Import</h3>
                </div>

                <ul class="text-sm text-indigo-800 space-y-3 list-disc pl-4 mb-5">
                    <li>Pastikan baris pertama Excel Anda berisi <b>judul kolom</b>.</li>
                    <li>Password siswa otomatis disamakan dengan <b>NISN</b>.</li>
                    <li>Sistem otomatis menolak email / NISN yang sudah terdaftar.</li>
                </ul>

                <h4 class="font-bold text-xs text-indigo-900 uppercase tracking-wider mb-2">Format Header Wajib:</h4>
                <div class="bg-white border border-indigo-200 rounded-xl overflow-hidden text-xs mb-5">
                    <table class="w-full text-left font-mono">
                        <thead class="bg-indigo-100/50 text-indigo-900">
                            <tr>
                                <th class="px-3 py-2 border-b border-r border-indigo-100">nama</th>
                                <th class="px-3 py-2 border-b border-r border-indigo-100">nisn</th>
                                <th class="px-3 py-2 border-b border-r border-indigo-100">email</th>
                                <th class="px-3 py-2 border-b border-indigo-100">jenis_kelamin</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            <tr>
                                <td class="px-3 py-2 border-r border-indigo-50">Budi</td>
                                <td class="px-3 py-2 border-r border-indigo-50">12345</td>
                                <td class="px-3 py-2 border-r border-indigo-50">a@b.com</td>
                                <td class="px-3 py-2">L</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" wire:click="downloadTemplate"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border-2 border-indigo-200 text-indigo-700 font-bold rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm group">
                    <i class="ri-file-download-line text-lg group-hover:-translate-y-0.5 transition-transform"></i>
                    Download Template Excel
                </button>

            </div>
        </div>

    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {

                Livewire.on('swal:success', (data) => {
                    let info = data[0];

                    Swal.fire({
                        title: info.title,
                        text: info.text,
                        icon: 'success',
                        confirmButtonColor: '#22c55e',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });
                });

                Livewire.on('swal:error', (data) => {
                    let info = data[0];

                    Swal.fire({
                        title: info.title,
                        text: info.text,
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Mengerti',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });
                });

            });
        </script>
    @endpush
</div>
