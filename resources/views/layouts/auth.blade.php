<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autenticación - Sistema Inventario')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-500 to-cyan-400 min-h-screen flex items-center justify-center p-4">
    
    <!-- Contenedor principal responsivo -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
        <!-- Aquí se renderizan las vistas (login / registro) -->
        @yield('content')
    </div>

</body>
</html>
