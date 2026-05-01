<div>
    <div
        class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl overflow-hidden mb-6 transition-all duration-300">

        <div
            class="p-5 border-b border-sky-50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class='ri-book-open-line text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Mata Pelajaran</h2>
                    <p class="text-sm text-gray-500">Kelola daftar kurikulum mapel umum & kejuruan.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">


                <x-ui.button @click="$dispatch('open-create-mapel')" class="w-full sm:w-auto" size="sm"
                    color="primary" icon="ri-add-line">
                    Tambah Mapel
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
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari kode atau nama mapel..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm custom-scrollbar">
                <table class="w-full text-left min-w-[600px]">
                    <thead
                        class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100 whitespace-nowrap text-xs sm:text-sm">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3 text-center w-32">Kode</th>
                            <th class="px-4 py-3 text-center">Nama Mata Pelajaran</th>
                            <th class="px-4 py-3 text-center">Kategori</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($mapels as $index => $row)
                            <tr class="hover:bg-indigo-50/30 transition-colors group"
                                wire:key="mapel-{{ $row->id }}">
                                <td class="px-4 py-3 text-center text-gray-500 whitespace-nowrap text-xs sm:text-sm">
                                    {{ $mapels->firstItem() + $index }}
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <code
                                        class="px-2 py-1 bg-slate-100 text-slate-700 rounded font-mono text-[11px] sm:text-xs border border-slate-200">
                                        {{ $row->kode_mapel }}
                                    </code>
                                </td>

                                <td
                                    class="px-4 py-3 text-center whitespace-nowrap text-xs sm:text-sm font-bold text-slate-700">
                                    {{ Str::title($row->nama_mapel) }}
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @php
                                        $kat = strtolower($row->kategori);
                                        $katColor =
                                            $kat === 'kejuruan'
                                                ? 'bg-purple-50 text-purple-700 border-purple-100'
                                                : ($kat === 'umum'
                                                    ? 'bg-sky-50 text-sky-700 border-sky-100'
                                                    : 'bg-emerald-50 text-emerald-700 border-emerald-100');
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-bold border shadow-sm {{ $katColor }}">
                                        {{ strtoupper($row->kategori) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5 sm:gap-2">
                                        <button @click="$dispatch('open-edit-mapel', { id: {{ $row->id }} });"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition-all flex items-center justify-center shadow-sm border border-yellow-100">
                                            <i class="ri-pencil-line text-xs sm:text-sm"></i>
                                        </button>

                                        <button type="button"
                                            onclick="konfirmasiHapusMapel({{ $row->id }}, @js($row->nama_mapel))"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm border border-red-100">
                                            <i class="ri-delete-bin-line text-xs sm:text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-4 border border-slate-100">
                                            <i class="ri-book-3-line text-4xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-800">Data Tidak Ditemukan</h3>
                                        <p class="text-sm text-slate-500">Coba cari kata kunci lain atau tambah mapel
                                            baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="w-full">
                {{ $mapels->links('components.ui.custom-pagination') }}
            </div>

        </div>
        <livewire:admin.mapel.form />
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

        function konfirmasiHapusMapel(id, nama) {
            Swal.fire({
                title: 'Hapus Mata Pelajaran?',
                html: `Kamu akan menghapus mapel <b class="text-rose-600">${nama}</b>.<br><small class="text-slate-500">.</small>`,
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
                    Livewire.dispatch('hapus-mapel', {
                        id: id
                    });
                }
            });
        }
    </script>
@endpush
