<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Iniciar sesión') · {{ config('app.name', 'MultiSoft') }}</title>
    <meta name="description" content="MultiSoft · Inventario y ventas para negocios">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf',
                            500: '#218786', 600: '#1d7874', 700: '#196663', 800: '#165b5c', 900: '#134e4a',
                        },
                    },
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .form-label { @apply block text-sm font-medium text-gray-700 mb-1.5; }
            .form-input { @apply block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20; }
            .btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:opacity-60; }
        }
    </style>
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-white font-sans antialiased text-gray-800">
    <div class="min-h-screen grid lg:grid-cols-2">
        {{-- Panel de marca --}}
        <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-slate-900 p-12 text-white">
            <div class="absolute inset-0 opacity-40" style="background: radial-gradient(60% 50% at 20% 10%, #218786 0%, transparent 70%), radial-gradient(50% 40% at 90% 90%, #196663 0%, transparent 70%);"></div>
            <div class="relative flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-lg font-semibold">MultiSoft</span>
            </div>

            <div class="relative max-w-md">
                <h2 class="text-3xl font-semibold leading-tight tracking-tight">Inventario, ventas y facturación electrónica en un solo lugar.</h2>
                <ul class="mt-8 space-y-4 text-sm text-slate-300">
                    @foreach(['Punto de venta rápido con lector de código de barras', 'Kardex y control de stock en tiempo real', 'Boletas y facturas electrónicas SUNAT', 'Reportes de ganancias y finanzas'] as $punto)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 shrink-0 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $punto }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="relative text-xs text-slate-500">© {{ date('Y') }} MultiSoft</p>
        </aside>

        {{-- Contenido --}}
        <main class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
