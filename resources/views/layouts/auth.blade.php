<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Autenticación - Taller Pro')</title>
    <meta name="description" content="Sistema de autenticación seguro para Taller Pro - Gestión de inventario profesional">
    
    <!-- Preload y optimizaciones -->
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts optimizadas -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS con configuración personalizada -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#218786',
                            600: '#1d7874ff',
                            700: '#196663',
                            800: '#165b5c',
                            900: '#134e4a',
                        },
                    },
                }
            }
        }
    </script>
    
    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Fondo animado mejorado */
        .animated-bg {
            background: linear-gradient(-45deg, #218786, #1d7874, #20b2aa, #48cae4, #218786);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Patrones geométricos de fondo */
        .bg-pattern {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 0%, transparent 50%);
            background-size: 100px 100px;
            animation: patternFloat 20s linear infinite;
        }
        
        @keyframes patternFloat {
            0% { background-position: 0% 0%; }
            100% { background-position: 100px 100px; }
        }
        
        /* Efectos de partículas flotantes */
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
            animation-duration: 6s;
        }
        
        .shape:nth-child(2) {
            top: 20%;
            right: 10%;
            animation-delay: 2s;
            animation-duration: 8s;
        }
        
        .shape:nth-child(3) {
            bottom: 10%;
            left: 20%;
            animation-delay: 4s;
            animation-duration: 7s;
        }
        
        .shape:nth-child(4) {
            bottom: 20%;
            right: 20%;
            animation-delay: 1s;
            animation-duration: 9s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(90deg); }
            50% { transform: translateY(0px) rotate(180deg); }
            75% { transform: translateY(-10px) rotate(270deg); }
        }
        
        /* Efectos de glassmorphism mejorado */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 8px 32px 0 rgba(31, 38, 135, 0.37),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }
        
        /* Animaciones de entrada */
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Loading overlay */
        .loading-overlay {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }
        
        /* Responsive improvements */
        @media (max-width: 640px) {
            .glass-card {
                margin: 1rem;
                backdrop-filter: blur(15px);
            }
            
            body {
                padding: 1rem;
            }
            
            .footer-badges {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
        
        /* Estados de enfoque mejorados */
        .focus-visible:focus-visible {
            outline: 2px solid #218786;
            outline-offset: 2px;
        }
        
        /* Scroll suave */
        html {
            scroll-behavior: smooth;
        }
        
        /* Prevenir zoom en inputs en iOS */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            select:focus,
            textarea:focus,
            input:focus {
                font-size: 16px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="animated-bg bg-pattern min-h-screen flex flex-col justify-center items-center p-4 relative overflow-hidden">
    
    <!-- Formas flotantes decorativas -->
    <div class="floating-shapes">
        <!-- Círculos -->
        <div class="shape w-32 h-32 bg-white rounded-full"></div>
        <div class="shape w-24 h-24 bg-white rounded-full"></div>
        <div class="shape w-20 h-20 bg-white rounded-full"></div>
        
        <!-- Rectángulos redondeados -->
        <div class="shape w-28 h-16 bg-white rounded-2xl"></div>
    </div>
    
    <!-- Loading overlay inicial -->
    <div id="loading-overlay" class="loading-overlay fixed inset-0 z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-4 bg-primary-500 rounded-2xl shadow-lg">
                <svg class="w-8 h-8 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
            </div>
            <p class="text-white font-medium">Cargando Taller Pro...</p>
        </div>
    </div>
    
    <!-- Contenedor principal centrado -->
    <div class="w-full max-w-md glass-card rounded-2xl shadow-2xl fade-in-up relative z-10 my-8" style="animation-delay: 0.2s">
        @yield('content')
    </div>
    
    <!-- Footer informativo -->
    <div class="mt-auto pb-4 z-10">
        <div class="text-center">
            <p class="text-white/80 text-sm font-medium mb-2">Taller Pro - Sistema de Inventario</p>
            <div class="flex items-center justify-center space-x-4 text-xs text-white/60 footer-badges">
                <div class="flex items-center mb-1">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Conexión SSL Segura
                </div>
                <div class="flex items-center mb-1">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Datos Protegidos
                </div>
                <div class="flex items-center mb-1">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Rendimiento Optimizado
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts críticos -->
    <script>
        // Ocultar loading screen
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const loadingOverlay = document.getElementById('loading-overlay');
                loadingOverlay.style.opacity = '0';
                loadingOverlay.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => {
                    loadingOverlay.style.display = 'none';
                }, 500);
            }, 800);
        });
        
        // Optimizaciones de performance
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
        
        // Prevenir zoom en dispositivos móviles
        document.addEventListener('touchstart', function(e) {
            if (e.touches.length > 1) {
                e.preventDefault();
            }
        }, { passive: false });
        
        // Manejo de errores global
        window.addEventListener('error', function(e) {
            console.error('Error capturado:', e);
        });
        
        // Lazy loading para imágenes (si las hay)
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>