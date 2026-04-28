<div>
    <div
        class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl overflow-hidden mb-6 transition-all duration-300">

        <div
            class="p-5 border-b border-sky-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class='ri-graduation-cap-line text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Data Guru</h2>
                    <p class="text-sm text-gray-500">Kelola data Guru SMK Katolik Santa.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <x-ui.button wire:click="exportExcel" class="w-full sm:w-auto" size="md" color="white"
                    icon="ri-download-2-line">
                    Ekspor Excel
                </x-ui.button>
                <x-ui.button @click="$dispatch('open-create-data-guru')" class="w-full sm:w-auto" size="md"
                    color="primary" icon="ri-add-line">
                    Tambah Baru
                </x-ui.button>
            </div>
        </div>

        <div class="p-5">
            <div class="bg-slate-50 p-5 rounded-xl border border-gray-100 mb-6 shadow-inner">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3 shrink-0">
                        <div
                            class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center text-gray-500">
                            <i class="ri-filter-3-line text-lg"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Filter & Pencarian</span>
                    </div>

                    <div class="relative w-full sm:w-80">
                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama, NIP..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100 ">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100 whitespace-nowrap">
                        <tr>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center w-10 sm:w-12">No</th>
                            <th class="px-2 sm:px-4 py-2 text-center sm:py-3">Guru</th>
                            <th class="px-2 sm:px-4 py-2 text-center sm:py-3">Jenis Kelamin</th>
                            <th class="px-4 py-3 text-centerpx-2 sm:px-4 text-center sm:py-3">Status</th>
                            <th class="px-2 sm:px-4 py-2 text-center sm:py-3">Email</th>
                            <th class="px-2 sm:px-4 py-2 text-center sm:py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($guru as $index => $row)
                            <tr class="hover:bg-sky-50/50 transition-colors group" wire:key="guru-{{ $row->id }}">

                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-500">
                                    {{ $guru->firstItem() + $index }}
                                </td>

                                <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <img src="{{ $row->user->avatar_url ?? 'https://ui-avatars.com/api/?background=e0f2fe&color=0284c7&name=' . urlencode($row->user->name ?? 'User') }}"
                                            alt="Avatar"
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-gray-800 text-[13px] sm:text-sm truncate max-w-[130px] sm:max-w-[200px]"
                                                title="{{ Str::title($row->user->name ?? '-') }}">
                                                {{ Str::title($row->user->name ?? '-') }}
                                            </span>
                                            <span
                                                class="text-sky-600 text-[10px] sm:text-xs truncate flex items-center gap-1 mt-0.5 font-medium">
                                                <i class='ri-id-card-line'></i> {{ $row->nip ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-2 sm:px-4 py-2 sm:py-33 text-center">
                                    @php
                                        $jk = $row->user->jenis_kelamin ?? 'L';
                                        $badgeColor =
                                            $jk === 'L' ? 'bg-sky-100 text-sky-700' : 'bg-pink-100 text-pink-700';
                                        $badgeText = $jk === 'L' ? 'Laki-laki' : 'Perempuan';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $badgeColor }}">
                                        {{ $badgeText }}
                                    </span>
                                </td>

                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                                    @php
                                        $status = strtolower($row->user->status ?? 'aktif');
                                        $statusColor =
                                            $status === 'aktif'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-red-100 text-red-700';
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusColor }} capitalize">
                                        {{ $status }}
                                    </span>
                                </td>

                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-600 text-sm">
                                    @if ($row->user->email ?? false)
                                        <div class="flex items-center justify-center gap-1.5">
                                            <i class="ri-mail-line text-sky-500"></i> {{ $row->user->email }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Tidak Tersedia</span>
                                    @endif
                                </td>



                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="$dispatch('open-edit-data-guru',{ id: {{ $row->id }}})"
                                            class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-400 hover:text-white transition-colors flex items-center justify-center"
                                            title="Edit Data">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button
                                            onclick="konfirmasiHapusDataGuru({{ $row->id }}, @js($row->user->name))"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center"
                                            title="Hapus Data">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mb-4">
                                            <i
                                                class="{{ $this->isFiltered ? 'ri-search-eye-line' : 'ri-folder-user-line' }} text-4xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">
                                            {{ $this->isFiltered ? 'Pencarian Tidak Ditemukan' : 'Belum Ada Data guru' }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm mx-auto">
                                            {{ $this->isFiltered ? 'Tidak ada data guru yang cocok dengan kata kunci tersebut.' : 'Saat ini belum ada satupun data guru yang terdaftar di dalam sistem.' }}
                                        </p>

                                        @if ($this->isFiltered)
                                            <x-ui.button wire:click="resetSemuaFilter" color="white"
                                                icon="ri-refresh-line">
                                                Reset Pencarian
                                            </x-ui.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="w-full">
                {{ $guru->links('components.ui.custom-pagination') }}
            </div>

        </div>
    </div>
    <livewire:admin.manajemen-pengguna.guru.form />

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

            function konfirmasiHapusDataGuru(id, nama) {
                Swal.fire({
                    title: 'Hapus Data Guru?',
                    html: `Kamu akan menghapus data Guru <b class="text-rose-600">${nama}</b>.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-[2rem] p-6 shadow-2xl',
                        confirmButton: 'bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-6 rounded-xl mx-2 transition-all',
                        cancelButton: 'bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-6 rounded-xl mx-2 transition-all'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('hapus-data-guru', {
                            id: id
                        });
                    }
                });
            }
        </script>
    @endpush
</div>
