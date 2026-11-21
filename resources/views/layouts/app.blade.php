<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Inicio') · {{ config('app.name', 'MultiSoft') }}</title>
    <meta name="description" content="@yield('description', 'Sistema de gestión de inventario y ventas para negocios')">

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
                        // Paleta corregida (antes 50/100 eran tonos oscuros y el texto no se leía)
                        primary: {
                            50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf',
                            500: '#218786', 600: '#1d7874', 700: '#196663', 800: '#165b5c', 900: '#134e4a',
                        },
                    },
                    boxShadow: {
                        soft: '0 1px 2px rgba(16,24,40,.04), 0 1px 3px rgba(16,24,40,.06)',
                    },
                },
            },
        };
    </script>

    {{-- Componentes reutilizables del sistema de diseño --}}
    <style type="text/tailwindcss">
        @layer components {
            .card { @apply bg-white rounded-2xl border border-gray-200/70 shadow-soft p-6; }
            .card-header { @apply mb-5; }
            .card-title { @apply text-base font-semibold text-gray-900; }
            .card-subtitle { @apply text-sm text-gray-500 mt-0.5; }

            .btn-primary { @apply inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:opacity-50; }
            .btn-secondary { @apply inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500; }
            .btn-danger { @apply inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition; }
            .btn-icon { @apply inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-primary-700 hover:bg-primary-50 transition; }
            .link { @apply text-sm font-semibold text-primary-700 hover:text-primary-800; }

            .form-label { @apply block text-sm font-medium text-gray-700 mb-1.5; }
            .form-input { @apply block w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30; }
            .form-file { @apply block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100; }
            .form-help { @apply mt-1 text-xs text-gray-500; }

            .choice { @apply flex flex-col gap-1 rounded-xl border-2 border-gray-200 p-4 cursor-pointer transition hover:border-primary-300; }
            .choice-active { @apply border-primary-500 bg-primary-50/60; }

            .badge { @apply inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold; }
            .badge-primary { @apply bg-primary-100 text-primary-800; }
            .badge-success { @apply bg-emerald-100 text-emerald-700; }
            .badge-warning { @apply bg-amber-100 text-amber-700; }
            .badge-danger { @apply bg-red-100 text-red-700; }
            .badge-info { @apply bg-sky-100 text-sky-700; }
            .badge-gray { @apply bg-gray-100 text-gray-600; }

            .alert { @apply flex items-start gap-3 rounded-2xl border p-4; }
            .alert-success { @apply bg-emerald-50 border-emerald-200 text-emerald-800; }
            .alert-warning { @apply bg-amber-50 border-amber-200 text-amber-800; }
            .alert-danger { @apply bg-red-50 border-red-200 text-red-800; }

            .table { @apply min-w-full text-sm; }
            .table thead th { @apply bg-gray-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500; }
            .table tbody td { @apply px-6 py-3.5 text-gray-700 border-t border-gray-100; }
            .table tbody tr { @apply transition-colors hover:bg-gray-50/70; }

            .stat-card { @apply bg-white rounded-2xl border border-gray-200/70 shadow-soft p-5; }
            .stat-icon { @apply w-10 h-10 rounded-xl flex items-center justify-center mb-4; }
            .stat-label { @apply text-sm font-medium text-gray-500; }
            .stat-value { @apply mt-1 text-2xl font-bold text-gray-900 tracking-tight; }
            .stat-foot { @apply mt-1 text-xs text-gray-500; }

            .empty-state { @apply py-8 text-center text-sm text-gray-500; }

            .nav-link { @apply flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white; }
            .nav-link-active { @apply bg-primary-500/15 text-white ring-1 ring-inset ring-primary-400/30; }
            .nav-section { @apply px-3 pt-5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500; }
        }
    </style>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148,163,184,.4); border-radius: 3px; }
        .bg-gradient-teal { background: linear-gradient(135deg, #218786 0%, #1d7874 100%); }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

    @stack('styles')
</head>
@php
    $usuarioActual = auth()->user();
    $empresaActual = $usuarioActual?->empresa;
    $esAdmin = $usuarioActual?->esAdmin();
    $rolNombre = ['admin' => 'Administrador', 'encargado' => 'Encargado', 'usuario' => 'Vendedor'][$usuarioActual->rol ?? ''] ?? ucfirst($usuarioActual->rol ?? '');

    $iconos = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'pos' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'truck' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
        'receipt' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
        'doc' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'cash' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'user-cog' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197',
        'compras' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9zm7 3v5m-2.5-2.5h5',
        'kardex' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'cog' =>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    ];

    $menu = [
        null => [
            ['Inicio', 'dashboard', 'dashboard', 'home'],
            ['Punto de venta', 'ventas.create', 'ventas.create', 'pos'],
        ],
        'Inventario' => [
            ['Productos', 'repuestos.index', 'repuestos.*', 'box'],
            ['Categorías', 'categorias.index', 'categorias.*', 'tag'],
            ['Proveedores', 'proveedores.index', 'proveedores.*', 'truck'],
            ['Kardex', 'kardex.index', 'kardex.*', 'kardex'],
        ],
        'Ventas' => [
            ['Historial de ventas', 'ventas.index', ['ventas.index', 'ventas.show', 'ventas.historial'], 'receipt'],
            ['Comprobantes SUNAT', 'comprobantes.index', 'comprobantes.*', 'doc'],
            ['Clientes', 'clientes.index', 'clientes.*', 'users'],
        ],
        'Gestión' => [
            ['Finanzas', 'finanzas.index', 'finanzas.*', 'cash'],
            ['Reportes', 'reportes.index', 'reportes.*', 'chart'],
        ],
    ];

    // Compras: administradores y encargados
    if (in_array($usuarioActual->rol ?? '', ['admin', 'encargado'])) {
        array_splice($menu['Inventario'], 1, 0, [['Compras', 'compras.index', 'compras.*', 'compras']]);
    }

    if ($esAdmin) {
        $menu['Administración'] = [
            ['Usuarios', 'usuarios.index', 'usuarios.*', 'user-cog'],
            ['Empresa y SUNAT', 'empresa.index', 'empresa.*', 'cog'],
        ];
    }
@endphp
<body class="bg-gray-50 font-sans antialiased text-gray-800">
<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    {{-- Fondo móvil --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         x-transition.opacity class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm lg:hidden"></div>

    {{-- ===================== SIDEBAR ===================== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-slate-900 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">

        {{-- Marca --}}
        <div class="flex items-center gap-3 px-5 h-16 shrink-0 border-b border-white/5">
            @if($empresaActual?->logo_url)
                <img src="{{ $empresaActual->logo_url }}" alt="" class="w-9 h-9 rounded-xl object-contain bg-white p-1">
            @else
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-900/40">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconos['box'] }}"/></svg>
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-bold text-white truncate">{{ $empresaActual->nombre_comercial ?? $empresaActual->nombre ?? 'MultiSoft' }}</p>
                <p class="text-[11px] text-slate-400">Inventario y ventas</p>
            </div>
            <button @click="sidebarOpen = false" class="ml-auto p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 lg:hidden" aria-label="Cerrar menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 overflow-y-auto custom-scrollbar px-3 pb-6">
            @foreach($menu as $seccion => $items)
                @if($seccion)
                    <p class="nav-section">{{ $seccion }}</p>
                @else
                    <div class="pt-4"></div>
                @endif
                <div class="space-y-0.5">
                    @foreach($items as [$label, $ruta, $patron, $icono])
                        @php $activo = request()->routeIs(...(array) $patron); @endphp
                        <a href="{{ route($ruta) }}" class="nav-link {{ $activo ? 'nav-link-active' : '' }}">
                            <svg class="w-5 h-5 shrink-0 {{ $activo ? 'text-primary-300' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $iconos[$icono] }}"/>
                            </svg>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        {{-- Ambiente SUNAT --}}
        @if($empresaActual?->facturacion_activa)
            <div class="mx-3 mb-3 rounded-xl px-3 py-2 text-xs {{ $empresaActual->esBeta() ? 'bg-amber-500/10 text-amber-300' : 'bg-emerald-500/10 text-emerald-300' }}">
                <span class="inline-block w-1.5 h-1.5 rounded-full mr-1.5 align-middle {{ $empresaActual->esBeta() ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                SUNAT: {{ $empresaActual->esBeta() ? 'modo pruebas' : 'producción' }}
            </div>
        @endif
    </aside>

    {{-- ===================== CONTENIDO ===================== --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 shrink-0 bg-white/80 backdrop-blur border-b border-gray-200/70 sticky top-0 z-20">
            <div class="h-full flex items-center gap-3 px-4 lg:px-8">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100" aria-label="Abrir menú">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <p class="text-sm font-semibold text-gray-900 truncate lg:hidden">@yield('page-title', config('app.name', 'MultiSoft'))</p>

                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('ventas.create') }}" class="hidden sm:inline-flex btn-primary !py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva venta
                    </a>

                    {{-- Menú de usuario --}}
                    <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                        <button @click="open = !open" class="flex items-center gap-2.5 rounded-xl p-1.5 pr-2 hover:bg-gray-100 transition">
                            <span class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-bold">
                                {{ mb_strtoupper(mb_substr($usuarioActual->nombre ?? 'U', 0, 1)) }}
                            </span>
                            <span class="hidden md:block text-left leading-tight">
                                <span class="block text-sm font-semibold text-gray-900 max-w-[140px] truncate">{{ $usuarioActual->nombre ?? '' }}</span>
                                <span class="block text-xs text-gray-500">{{ $rolNombre }}</span>
                            </span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false" x-transition.origin.top.right
                             class="absolute right-0 mt-2 w-56 rounded-2xl bg-white border border-gray-200 shadow-xl p-1.5">
                            <div class="px-3 py-2 border-b border-gray-100 mb-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $usuarioActual->nombre ?? '' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $usuarioActual->correo ?? '' }}</p>
                            </div>
                            @if($esAdmin)
                                <a href="{{ route('empresa.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Configuración de empresa</a>
                                <a href="{{ route('usuarios.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Usuarios</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="max-w-7xl mx-auto px-4 py-6 lg:px-8 lg:py-8">
                @hasSection('page-title')
                    <div class="hidden lg:block mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">@yield('page-title')</h1>
                        @hasSection('page-description')
                            <p class="mt-1 text-sm text-gray-500">@yield('page-description')</p>
                        @endif
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

<script>
    // Notificaciones (los textos se pasan como JSON para evitar romper el JS o inyectar HTML)
    document.addEventListener('DOMContentLoaded', () => {
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timerProgressBar: true,
            showCloseButton: true,
        });

        const mensajes = [
            ['success', @json(session('success')), 4000],
            ['error', @json(session('error')), 6000],
            ['warning', @json(session('warning')), 5000],
        ];

        mensajes.forEach(([icon, text, timer]) => {
            if (text) toast.fire({ icon, text, timer });
        });
    });
</script>

@stack('scripts')
</body>
</html>
