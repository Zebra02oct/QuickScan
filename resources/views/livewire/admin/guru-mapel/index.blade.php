<div>
    <div
        class="bg-white/80 backdrop-blur-xl border border-sky-100 shadow-sm rounded-2xl overflow-hidden mb-6 transition-all duration-300">

        <div
            class="p-5 border-b border-sky-50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class='ri-calendar-todo-line text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Manajemen Mapel Guru</h2>
                    <p class="text-sm text-gray-500">Atur penugasan guru, mata pelajaran, dan kelas secara terstruktur..
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <x-ui.button @click="$dispatch('open-create-jadwal')" class="w-full sm:w-auto" size="md"
                    color="primary" icon="ri-add-line">
                    Tambah
                </x-ui.button>
            </div>
        </div>

        <div class="p-4 sm:p-5">

            <div class="bg-slate-50 p-4 sm:p-5 rounded-xl border border-gray-100 mb-6 shadow-inner">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
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
                            placeholder="Cari nama guru, mapel..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-4 border-t border-gray-200/60">



                    <div>
                        <select wire:model.live="filter_kelas"
                            class="w-full py-2 px-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600 bg-white">
                            <option value="">Semua Kelas</option>
                            @foreach ($list_kelas as $kls)
                                <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select wire:model.live="filter_mapel"
                            class="w-full py-2 px-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all shadow-sm text-sm text-gray-600 bg-white">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($list_mapel as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->kode_mapel }} -
                                    {{ ucwords($mapel->nama_mapel) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
            <div class="flex flex-col gap-4">
                @forelse ($gurus as $guru)
                    <div x-data="{ open: false }"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden transition-all duration-200"
                        :class="open ? 'ring-2 ring-orange-500/20 border-orange-200' : 'hover:border-gray-300'">

                        <div @click="open = !open"
                            class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 text-orange-700 rounded-full flex items-center justify-center font-bold text-lg shadow-inner border border-orange-200/50">
                                    {{ strtoupper(substr($guru->user->name ?? 'G', 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-base sm:text-lg">
                                        {{ $guru->user->name ?? 'Guru Dihapus' }}</h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span
                                            class="px-2 py-0.5 bg-sky-50 text-sky-600 text-[10px] sm:text-xs font-semibold rounded-md border border-sky-100">
                                            {{ $guru->guruMapel->count() }} Jadwal Mengajar
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-down-s-line text-xl transition-transform duration-300"
                                    :class="open ? 'rotate-180 text-orange-500' : ''"></i>
                            </div>
                        </div>

                        <div x-show="open" x-transition.opacity.duration.300ms style="display: none;"
                            class="border-t border-gray-100 bg-slate-50/50 p-4">
                            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm  bg-white">
                                <table class="w-full text-left min-w-[550px]">
                                    <thead
                                        class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 whitespace-nowrap text-xs">
                                        <tr>
                                            <th class="px-4 py-3 w-10 text-center">No</th>
                                            <th class="px-4 py-3">Mata Pelajaran</th>
                                            <th class="px-4 py-3 text-center">Kelas</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($guru->guruMapel as $idx => $jadwal)
                                            <tr class="hover:bg-orange-50/40 transition-colors"
                                                wire:key="jadwal-{{ $jadwal->id }}">
                                                <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                                    {{ $idx + 1 }}</td>


                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs sm:text-sm font-semibold text-sky-700">
                                                        {{ ucwords($jadwal->mapel->nama_mapel ?? '-') }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 font-mono">
                                                        {{ $jadwal->mapel->kode_mapel ?? '-' }}
                                                    </div>
                                                </td>

                                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                                    <span
                                                        class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                        {{ $jadwal->kelas->nama_kelas ?? '-' }}
                                                    </span>
                                                </td>

                                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button
                                                            @click="$dispatch('open-edit-jadwal', { id: {{ $jadwal->id }} });"
                                                            class="w-7 h-7 rounded bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition-all flex items-center justify-center border border-yellow-100 shadow-sm tooltip"
                                                            data-tip="Edit">
                                                            <i class="ri-pencil-line text-xs"></i>
                                                        </button>
                                                        <button type="button"
                                                            onclick="konfirmasiHapusJadwal({{ $jadwal->id }})"
                                                            class="w-7 h-7 rounded bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-red-100 shadow-sm tooltip"
                                                            data-tip="Hapus">
                                                            <i class="ri-delete-bin-line text-xs"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <div class="flex flex-col items-center justify-center">
                            <div
                                class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-4 border border-slate-100">
                                <i class="ri-user-unfollow-line text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Tidak Ada Data</h3>

                        </div>
                    </div>
                @endforelse
            </div>

            @if ($gurus->hasPages())
                <div class="mt-6 border-t border-slate-100 pt-4">
                    {{ $gurus->links() }}
                </div>
            @endif
        </div>

        <livewire:admin.jadwal.form />
    </div>
</div>

@push('scripts')
    <script>
        function konfirmasiHapusJadwal(id) {
            Swal.fire({
                title: 'Hapus Plotting?',
                html: `Kamu akan menghapus plotting mapel dan kelas ini.<br><small class="text-slate-500">Data absensi terkait mungkin akan terdampak.</small>`,
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
                    Livewire.dispatch('hapus-jadwal', {
                        id: id
                    });
                }
            });
        }
    </script>
@endpush
