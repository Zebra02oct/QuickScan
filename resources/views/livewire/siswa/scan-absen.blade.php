<div class="max-w-md mx-auto py-8 px-4 sm:px-6">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">

        <div class="bg-sky-600 p-6 text-center text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

            <h2 class="text-2xl font-black relative z-10">Scan Kehadiran</h2>
            <p class="text-sky-100 text-sm mt-1 relative z-10">Arahkan kamera ke layar proyektor</p>
        </div>

        <div class="p-6">
            @if ($pesan_error)
                <div
                    class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl flex items-start gap-3 animate-fade-in-down">
                    <i class="ri-error-warning-fill text-xl"></i>
                    <p class="text-sm font-semibold mt-0.5">{{ $pesan_error }}</p>
                </div>
            @endif

            @if ($pesan_sukses)
                <div
                    class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-start gap-3 animate-fade-in-down">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                    <p class="text-sm font-semibold mt-0.5">{{ $pesan_sukses }}</p>
                </div>
            @endif

            <div id="reader"
                class="w-full bg-black rounded-2xl overflow-hidden shadow-inner border-4 border-slate-100 min-h-[300px]">
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs text-slate-400 font-medium"><i class="ri-lock-line"></i> Token otomatis kedaluwarsa
                    dalam 10 detik</p>
            </div>

            <div class="flex items-center gap-3 my-6">
                <div class="h-px bg-gray-200 flex-1"></div>
                <span class="text-xs font-bold text-gray-400 uppercase">Atau (Mode Testing)</span>
                <div class="h-px bg-gray-200 flex-1"></div>
            </div>

            <div class="flex gap-2">
                <input type="text" wire:model="token_manual" placeholder="Masukkan Token QR..."
                    class="flex-1 px-4 py-3 bg-slate-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 outline-none text-sm font-mono">
                <button wire:click="prosesAbsen(token_manual)" wire:loading.attr="disabled"
                    class="px-5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center">
                    <i wire:loading.remove wire:target="prosesAbsen" class="ri-send-plane-fill"></i>
                    <i wire:loading wire:target="prosesAbsen" class="ri-loader-4-line animate-spin"></i>
                </button>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {

            const html5QrCode = new Html5Qrcode("reader");
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };

            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                html5QrCode.pause();

                @this.call('prosesAbsen', decodedText).then(() => {
                    setTimeout(() => {
                        html5QrCode.resume();
                    }, 3000);
                });
            };

            html5QrCode.start({
                    facingMode: "environment"
                },
                config,
                qrCodeSuccessCallback
            ).catch((err) => {
                console.error("Error kamera:", err);
                document.getElementById('reader').innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-slate-500 p-6">
                        <i class="ri-camera-off-line text-4xl mb-2"></i>
                        <p class="text-sm font-bold text-rose-500">Gagal Membuka Kamera</p>
                        <p class="text-xs mt-1 text-center">Pastikan Anda telah memberikan izin kamera di browser (Chrome/Safari).</p>
                    </div>
                `;
            });

        });
    </script>
</div>
