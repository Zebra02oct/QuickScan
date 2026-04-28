<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Absensi' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.webp') }}" />
    <meta name="description" content="Aplikasi Absensi Digital Terintegrasi" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css">

    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slideDown {
            animation: slideDown 0.2s ease-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>

<body class="text-gray-800 antialiased font-sans">
    <div id="root">
        <div class="min-h-screen flex bg-slate-50">

            @include('layouts.sidebar')

            <div
                class="flex-1 flex flex-col min-h-screen transition-all duration-300 md:ml-[260px] bg-gradient-to-br from-sky-50 via-white to-sky-100/80 relative overflow-hidden">

                @include('layouts.header')

                <main class="flex-1 w-full p-6 lg:p-8  relative z-10">

                    <div
                        class="absolute top-0 right-0 -mr-24 -mt-24 w-80 h-80 rounded-full bg-sky-400/40 blur-3xl pointer-events-none">
                    </div>

                    <div
                        class="absolute bottom-10 left-10 w-72 h-72 rounded-full bg-sky-500/30 blur-3xl pointer-events-none">
                    </div>

                    <div class="relative z-20">
                        {{ $slot }}
                    </div>

                </main>

            </div>

        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
