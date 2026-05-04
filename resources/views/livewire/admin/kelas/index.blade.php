<div>
    <div
        class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl overflow-hidden mb-6 transition-all duration-300">

        <div
            class="p-5 border-b border-sky-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class='ri-presentation-fill text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Manajemen Kelas</h2>
                    <p class="text-sm text-gray-500">Kelola data ruangan kelas dan jurusan.</p>
                </div>
            </div>

            <div class="w-full md:w-auto">
                <x-ui.button @click="$dispatch('open-create-kelas')" class="w-full sm:w-auto" size="sm"
                    color="primary" icon="ri-add-line">
                    Tambah Kelas
                </x-ui.button>
            </div>
        </div>

        <div class="p-4 sm:p-5">
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
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama Kelas..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm custom-scrollbar">
                <table class="w-full text-left min-w-[500px]">

                    <thead
                        class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100 whitespace-nowrap text-xs sm:text-sm">
                        <tr>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center w-10 sm:w-12">No</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-left">Nama Kelas</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Tingkat</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Jurusan</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Wali</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Jumlah Siswa</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Status</th>
                            <th class="px-3 sm:px-4 py-2.5 sm:py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($kelases as $index => $row)
                            <tr class="hover:bg-sky-50/50 transition-colors group" wire:key="kelas-{{ $row->id }}">

                                <td
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-center text-gray-500 whitespace-nowrap text-xs sm:text-sm">
                                    {{ $kelases->firstItem() + $index }}
                                </td>

                                <td
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap text-xs sm:text-sm font-bold text-slate-700">
                                    {{ $row->nama_kelas }}
                                </td>


                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <span
                                        class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                                        {{ $row->tingkat }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <span
                                        class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                                        {{ $row->jurusan }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <span
                                        class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                                        {{ $row->waliKelas->user->name }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    @if ($row->siswas_count > 0)
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                                            <i class="ri-group-line mr-1"></i> {{ $row->siswas_count }} Siswa
                                        </span>
                                    @else
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 shadow-sm">
                                            <i class="ri-user-unfollow-line mr-1"></i> Kosong
                                        </span>
                                    @endif
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    @if ($row->is_active)
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-lg text-[11px] sm:text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 shadow-sm">
                                            Non Aktif
                                        </span>
                                    @endif
                                </td>


                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5 sm:gap-2">

                                        <button @click="$dispatch('open-edit-kelas', { id: {{ $row->id }} });"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm border border-yellow-100 hover:border-transparent"
                                            title="Edit Data">
                                            <i class="ri-pencil-line text-xs sm:text-sm"></i>
                                        </button>


                                        <button
                                            onclick="konfirmasiHapusKelas({{ $row->id }}, @js($row->nama_kelas))"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center text-xs sm:text-base"
                                            title="Hapus Data">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 sm:p-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3 sm:mb-4 shadow-inner border border-slate-100">
                                            <i class="ri-door-open-line text-3xl sm:text-4xl"></i>
                                        </div>
                                        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1">
                                            Tidak Ada Data Kelas
                                        </h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="w-full">
                {{ $kelases->links('components.ui.custom-pagination') }}
            </div>

        </div>

        <livewire:admin.kelas.form />
    </div>
</div>

@push('scripts')
    <script>
        function konfirmasiHapusKelas(id, namaKelas) {
            Swal.fire({
                title: 'Hapus Kelas?',
                html: `
            <div class="text-slate-600 mb-2 text-sm leading-relaxed">
                Apakah Anda yakin ingin menghapus kelas <b class="text-rose-600">${namaKelas}</b>?
            </div>
         
            `,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: '<i class="ri-delete-bin-line mr-1"></i> Ya, Hapus Kelas',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[2rem] shadow-2xl border border-slate-100 p-6',
                    title: 'text-xl font-bold text-slate-800',
                    htmlContainer: 'text-base m-0 p-0',
                    actions: 'w-full flex gap-3 mt-6',
                    confirmButton: 'flex-1 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl px-5 py-3 transition-colors shadow-sm',
                    cancelButton: 'flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl px-5 py-3 transition-colors'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('hapus-data-kelas', {
                        id: id
                    });
                }
            });
        }

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
