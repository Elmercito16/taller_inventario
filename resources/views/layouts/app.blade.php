<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Taller Inventario') }}</title>
    <meta name="description" content="@yield('description', 'Sistema de gestión de inventario y ventas para talleres')">
    
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#1b8c72ff',
                            100: '#0b8a6fff',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#218786',
                            600: '#1d7874',
                            700: '#196663',
                            800: '#165b5c',
                            900: '#134e4a',
                        },
                    },
                    animation: {
                        'slide-in': 'slideIn 0.3s ease-out',
                        'fade-in': 'fadeIn 0.2s ease-out',
                        'bounce-subtle': 'bounceSubtle 0.5s ease-out',
                    },
                    keyframes: {
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        bounceSubtle: {
                            '0%, 20%, 50%, 80%, 100%': { transform: 'translateY(0)' },
                            '40%': { transform: 'translateY(-2px)' },
                            '60%': { transform: 'translateY(-1px)' },
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* ... (tu CSS personalizado de scrollbar, gradient, etc. va aquí) ... */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

        .focus-visible:focus-visible { outline: 2px solid #218786; outline-offset: 2px; }
        
        .bg-gradient-teal { background: linear-gradient(135deg, #218786 0%, #1d7874 100%); }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div id="loading-screen" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
            <p class="text-gray-600 text-sm">Cargando...</p>
        </div>
    </div>

    <div x-data="{ 
        sidebarOpen: false, 
        currentTime: new Date().toLocaleString('es-PE'),
        user: {
            name: '{{ session('nombre', 'Usuario') }}',
            role: '{{ session('rol', 'usuario') }}',
            avatar: '{{ session('avatar', asset('default-avatar.png')) }}'
        }
     }" 
     x-init="setInterval(() => currentTime = new Date().toLocaleString('es-PE'), 1000)"
     class="flex h-screen bg-gray-50">

        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden">
        </div>

        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed z-30 inset-y-0 left-0 w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col border-r border-gray-200"
        >
            <div class="p-6 bg-gradient-teal text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold">Taller Pro</h1>
                            <p class="text-xs text-teal-100 opacity-90">Inventario</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-teal-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                        <span class="text-primary-700 font-semibold text-sm" x-text="user.name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></p>
                        <p class="text-xs text-gray-500 capitalize" x-text="user.role"></p>
                    </div>
                    <div class="w-3 h-3 bg-primary-400 rounded-full ring-2 ring-white"></div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : 'text-gray-700 hover:text-primary-700' }}">
                    <svg class="mr-3 h-5 w-5 transition-colors group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v4M16 5v4"/>
                    </svg>
                    Dashboard
                </a>

                <div x-data="{ open: {{ request()->routeIs('repuestos.*', 'categorias.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="group w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-200 focus-visible">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Productos
                        </div>
                        <svg class="h-4 w-4 text-gray-400 transform transition-transform duration-200" 
                             :class="{'rotate-90': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('repuestos.index') }}" 
                           class="group flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-50 hover:text-primary-700 transition-colors {{ request()->routeIs('repuestos.*') ? 'text-primary-700 bg-primary-50' : '' }}">
                            <span class="w-2 h-2 bg-gray-300 rounded-full mr-3 group-hover:bg-primary-500 {{ request()->routeIs('repuestos.*') ? 'bg-primary-500' : '' }}"></span>
                            Lista de Repuestos
                        </a>
                        <a href="{{ route('categorias.index') }}" 
                           class="group flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-50 hover:text-primary-700 transition-colors {{ request()->routeIs('categorias.*') ? 'text-primary-700 bg-primary-50' : '' }}">
                            <span class="w-2 h-2 bg-gray-300 rounded-full mr-3 group-hover:bg-primary-500 {{ request()->routeIs('categorias.*') ? 'bg-primary-500' : '' }}"></span>
                            Categorías
                        </a>
                    </div>
                </div>

                @foreach([
                    ['route' => 'compras.*', 'href' => '#', 'label' => 'Compras', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                    ['route' => 'ventas.*', 'href' => route('ventas.index'), 'label' => 'Ventas', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['route' => 'clientes.*', 'href' => route('clientes.index'), 'label' => 'Clientes', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.50 11-5 0 2.5 2.5 0 015 0z'],
                    ['route' => 'proveedores.*', 'href' => route('proveedores.index'), 'label' => 'Proveedores', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['route' => 'caja.*', 'href' => '#', 'label' => 'Caja', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['route' => 'gastos.*', 'href' => '#', 'label' => 'Gastos', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1']
                ] as $item)
                    <a href="{{ $item['href'] }}" 
                       class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs($item['route']) ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-500' : 'text-gray-700' }}">
                        <svg class="mr-3 h-5 w-5 transition-colors group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-gray-200">
                <div class="text-xs text-gray-500 mb-3" x-text="currentTime"></div>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-red-50 hover:text-red-700 transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                <div class="flex items-center justify-between px-4 py-3 lg:px-6">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors focus-visible">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <nav class="hidden md:flex items-center space-x-2 text-sm">
                            @hasSection('breadcrumbs')
                                @yield('breadcrumbs')
                            @else
                                <span class="text-gray-500">Dashboard</span>
                            @endif
                        </nav>
                    </div>

                    <div class="flex-1 text-center lg:text-left">
                        <h1 class="text-xl font-semibold text-gray-900 lg:hidden">
                            @yield('page-title', 'Dashboard')
                        </h1>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors focus-visible">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5a50.002 50.002 0 00-7 0L6 17h9zM13 8V6a3 3 0 00-6 0v2m6 0a3 3 0 013 3v3.5M7 8a3 3 0 00-3 3v3.5"/>
                            </svg>
                        </button>
                        
                        <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors focus-visible">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-6 overflow-y-auto custom-scrollbar bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    <div class="hidden lg:block mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                        @hasSection('page-description')
                            <p class="mt-1 text-gray-600">@yield('page-description')</p>
                        @endif
                    </div>
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        window.onload = function() {
            document.getElementById('loading-screen').style.display = 'none';
        };

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Esperar a que SweetAlert2 esté disponible
        const checkSwal = () => {
            if (typeof Swal !== 'undefined') {
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{{ session('success') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        background: '#f0fdfa',
                        color: '#134e4a'
                    });
                @endif

                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ session('error') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        background: '#fef2f2',
                        color: '#7f1d1d'
                    });
                @endif

                @if(session('warning'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: '{{ session('warning') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4500,
                        timerProgressBar: true,
                        background: '#fffbeb',
                        color: '#78350f'
                    });
                @endif
            } else {
                setTimeout(checkSwal, 100);
            }
        };
        checkSwal();
    });
    </script>

    @stack('scripts')
</body>
</html>