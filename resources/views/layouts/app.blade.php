<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller Inventario</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- AlpineJS para submenus -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 font-sans flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-700 text-white flex-shrink-0 flex flex-col justify-between">
        <div>
            <div class="p-6 text-xl font-bold border-b border-blue-600">
                Taller Inventario
            </div>

            <nav class="mt-6">
                <!-- Dashboard -->
                <a href="#" class="flex items-center py-3 px-6 hover:bg-blue-600">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Productos con submenu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center w-full py-3 px-6 hover:bg-blue-600 focus:outline-none">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7"></path>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 17h6M9 21h6"></path>
                        </svg>
                        Productos
                        <svg class="ml-auto w-4 h-4 transition-transform transform"
                             :class="{'rotate-90': open}" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="bg-blue-600 ml-8">
                        <a href="{{ route('repuestos.index') }}" class="block py-2 px-4 hover:bg-blue-500">Lista de Repuestos</a>
                        <a href="{{ route('categorias.index') }}" class="block py-2 px-4 hover:bg-blue-500">Categorías</a>
                    </div>
                </div>

                <!-- Compras -->
                <a href="#" class="flex items-center py-3 px-6 hover:bg-blue-600 mt-2">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h18v6H3V3z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 9h18v12H3V9z"></path>
                    </svg>
                    Compras
                </a>

                <!-- Ventas -->
                <a href="{{ route('ventas.index') }}" class="flex items-center py-3 px-6 hover:bg-blue-600 mt-2">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7h18M3 12h18M3 17h18"></path>
                    </svg>
                    Ventas
                </a>

                <!-- Clientes -->
                <a href="{{ route('clientes.index') }}" class="flex items-center py-3 px-6 hover:bg-blue-600 mt-2">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Clientes
                </a>

                <!-- Proveedores -->
                <a href="{{ route('proveedores.index') }}" class="flex items-center py-3 px-6 hover:bg-blue-600 mt-2">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4v16m8-8H4"></path>
                    </svg>
                    Proveedores
                </a>

                <!-- Caja -->
                <a href="#" class="flex items-center py-3 px-6 hover:bg-blue-600 mt-2">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8v4l3 3"></path>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20 12a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                    </svg>
                    Caja
                </a>

                <!-- Gastos -->
                <a href="#" class="flex items-center py-3 px-6 hover:bg-blue-600 mt-2">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8c-3 0-5 2-5 5s2 5 5 5 5-2 5-5-2-5-5-5z"></path>
                    </svg>
                    Gastos
                </a>
            </nav>
        </div>

        <!-- Botón Cerrar Sesión -->
        <!-- Botón Cerrar Sesión -->
<div class="p-6 border-t border-blue-600">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center py-2 px-4 bg-blue-600 rounded-lg hover:bg-blue-800 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V7m0 0V6"></path>
            </svg>
            Cerrar Sesión
        </button>
    </form>
</div>
    </aside>

    <!-- Contenido principal -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    <!-- SweetAlert2 Flash Messages -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#2563eb'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#114cf0ff'
                });
            @endif
        });
    </script>

</body>
</html>
