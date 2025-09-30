@extends('layouts.app')

@section('title', 'Historial de Compras')

@push('meta')
<!-- SEO Meta Tags -->
<meta name="description" content="Historial completo de compras y transacciones del cliente">
<meta name="keywords" content="historial, compras, ventas, transacciones, cliente">
<meta property="og:title" content="Historial de Compras - Sistema de Inventario">
<meta property="og:description" content="Revisa el historial completo de transacciones">
@endpush

@push('styles')
<style>
    /* ========== Variables del Sistema ========== */
    :root {
        --primary: #218786;
        --primary-dark: #1a6b6a;
        --primary-light: #2fa5a4;
        --primary-soft: #e6f4f4;
        --primary-10: #21878610;
        --primary-20: #21878620;
        
        --success: #10b981;
        --success-soft: #10b98110;
        --warning: #f59e0b;
        --warning-soft: #f59e0b10;
        --danger: #ef4444;
        --danger-soft: #ef444410;
        --info: #3b82f6;
        --info-soft: #3b82f610;
        
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        
        --radius: 0.5rem;
        --radius-lg: 0.75rem;
    }

    /* ========== Animaciones Globales ========== */
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* ========== Clases Base ========== */
    * {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ========== Componentes del Sistema ========== */
    .card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    .card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .stats-card {
        background: white;
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    .stats-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        border-color: var(--primary-20);
    }

    /* Stagger animation for cards */
    .stats-card:nth-child(1) { animation-delay: 0.05s; }
    .stats-card:nth-child(2) { animation-delay: 0.1s; }
    .stats-card:nth-child(3) { animation-delay: 0.15s; }
    .stats-card:nth-child(4) { animation-delay: 0.2s; }

    /* ========== Botones ========== */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1;
        border-radius: var(--radius);
        transition: all 0.2s ease;
        cursor: pointer;
        white-space: nowrap;
        user-select: none;
        position: relative;
        overflow: hidden;
    }

    .btn:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px var(--primary-20);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: 1px solid var(--primary);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(33, 135, 134, 0.25);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: white;
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }

    .btn-secondary:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
        box-shadow: var(--shadow-sm);
    }

    /* ========== Iconos ========== */
    .icon-box {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-box-sm {
        width: 2rem;
        height: 2rem;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ========== Badges ========== */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
        line-height: 1;
    }

    .badge-success {
        background: var(--success-soft);
        color: var(--success);
    }

    .badge-warning {
        background: var(--warning-soft);
        color: var(--warning);
    }

    .badge-danger {
        background: var(--danger-soft);
        color: var(--danger);
    }

    /* ========== Accordion ========== */
    .accordion-item {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 0.75rem;
    }

    .accordion-item:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--primary-20);
    }

    .accordion-header {
        padding: 1rem 1.25rem;
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--gray-50);
        transition: background 0.2s ease;
    }

    .accordion-header:hover {
        background: var(--primary-10);
    }

    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
    }

    .accordion-content.active {
        max-height: 500px;
        opacity: 1;
    }

    .accordion-body {
        padding: 1.25rem;
        background: white;
        border-top: 1px solid var(--gray-200);
    }

    /* ========== Modal ========== */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 40;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-backdrop.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: var(--radius-lg);
        max-width: 28rem;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow-xl);
        transform: scale(0.95) translateY(10px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-backdrop.active .modal-content {
        transform: scale(1) translateY(0);
    }

    /* ========== Breadcrumbs ========== */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-500);
        margin-bottom: 1.5rem;
    }

    .breadcrumb a {
        color: var(--gray-500);
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .breadcrumb a:hover {
        color: var(--primary);
    }

    .breadcrumb-separator {
        color: var(--gray-400);
    }

    /* ========== Loading State ========== */
    .skeleton {
        background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ========== Responsive ========== */
    @media (max-width: 640px) {
        .stats-card {
            padding: 1rem;
        }
        
        .card {
            padding: 1rem;
        }
    }

    /* ========== Print Styles ========== */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumbs -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <svg class="w-4 h-4 breadcrumb-separator" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('clientes.index') }}">Clientes</a>
        <svg class="w-4 h-4 breadcrumb-separator" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span aria-current="page" style="color: var(--gray-900); font-weight: 500;">Historial</span>
    </nav>

    <!-- Header Card -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="icon-box" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold" style="color: var(--gray-900);">
                        Historial de Compras
                    </h1>
                    <p class="text-sm mt-1" style="color: var(--gray-600);">
                        Cliente: <span class="font-semibold" style="color: var(--primary);">{{ $cliente->nombre }}</span>
                    </p>
                    <p class="text-xs mt-0.5" style="color: var(--gray-500);">
                        DNI: {{ $cliente->dni }}
                    </p>
                </div>
            </div>
            <button onclick="openFilterModal()" class="btn btn-primary no-print">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtrar
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Ventas -->
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p style="color: var(--gray-600); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                        Total Ventas
                    </p>
                    <p style="color: var(--gray-900); font-size: 1.875rem; font-weight: 700; margin-top: 0.25rem;">
                        {{ $ventas->count() }}
                    </p>
                    <p style="color: var(--gray-500); font-size: 0.75rem; margin-top: 0.25rem;">
                        Transacciones
                    </p>
                </div>
                <div class="icon-box" style="background: var(--info-soft);">
                    <svg class="w-5 h-5" style="color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Monto Total -->
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p style="color: var(--gray-600); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                        Monto Total
                    </p>
                    <p style="color: var(--success); font-size: 1.875rem; font-weight: 700; margin-top: 0.25rem;">
                        S/ {{ number_format($ventas->sum('total'), 2) }}
                    </p>
                    <p style="color: var(--gray-500); font-size: 0.75rem; margin-top: 0.25rem;">
                        Acumulado
                    </p>
                </div>
                <div class="icon-box" style="background: var(--success-soft);">
                    <svg class="w-5 h-5" style="color: var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Promedio -->
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p style="color: var(--gray-600); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                        Promedio
                    </p>
                    <p style="color: var(--primary); font-size: 1.875rem; font-weight: 700; margin-top: 0.25rem;">
                        S/ {{ $ventas->count() > 0 ? number_format($ventas->avg('total'), 2) : '0.00' }}
                    </p>
                    <p style="color: var(--gray-500); font-size: 0.75rem; margin-top: 0.25rem;">
                        Por compra
                    </p>
                </div>
                <div class="icon-box" style="background: var(--primary-10);">
                    <svg class="w-5 h-5" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Última Compra -->
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p style="color: var(--gray-600); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                        Última Compra
                    </p>
                    <p style="color: var(--gray-900); font-size: 1.125rem; font-weight: 700; margin-top: 0.25rem;">
                        {{ optional($ventas->first())->fecha ? \Carbon\Carbon::parse($ventas->first()->fecha)->format('d/m/Y') : '—' }}
                    </p>
                    <p style="color: var(--gray-500); font-size: 0.75rem; margin-top: 0.25rem;">
                        {{ optional($ventas->first())->fecha ? \Carbon\Carbon::parse($ventas->first()->fecha)->diffForHumans() : 'Sin compras' }}
                    </p>
                </div>
                <div class="icon-box" style="background: rgba(147, 51, 234, 0.1);">
                    <svg class="w-5 h-5" style="color: rgb(147, 51, 234);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Transacciones -->
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="icon-box" style="background: rgba(251, 146, 60, 0.1);">
                <svg class="w-5 h-5" style="color: rgb(251, 146, 60);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--gray-900);">
                    Historial de Transacciones
                </h2>
                <p class="text-sm" style="color: var(--gray-600);">
                    {{ $ventas->count() }} transacciones encontradas
                </p>
            </div>
        </div>

        @if($ventas->count() > 0)
            <div class="space-y-3">
                @foreach ($ventas as $venta)
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion('venta-{{ $venta->id }}')">
                            <div class="flex items-center gap-3">
                                <div class="icon-box-sm" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                                    <span class="text-white text-xs font-bold">#{{ $venta->id }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-sm" style="color: var(--gray-900);">
                                        Compra #{{ $venta->id }}
                                    </h3>
                                    <p class="text-xs" style="color: var(--gray-500); margin-top: 0.125rem;">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p class="font-bold text-sm" style="color: var(--gray-900);">
                                        S/ {{ number_format($venta->total, 2) }}
                                    </p>
                                    @if($venta->estado === 'pagado')
                                        <span class="badge badge-success" style="margin-top: 0.25rem;">Pagado</span>
                                    @elseif($venta->estado === 'pendiente')
                                        <span class="badge badge-warning" style="margin-top: 0.25rem;">Pendiente</span>
                                    @else
                                        <span class="badge badge-danger" style="margin-top: 0.25rem;">{{ ucfirst($venta->estado) }}</span>
                                    @endif
                                </div>
                                <svg id="arrow-{{ $venta->id }}" 
                                     class="w-5 h-5 transition-transform duration-300" 
                                     style="color: var(--gray-400);"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        <div id="venta-{{ $venta->id }}" class="accordion-content">
                            <div class="accordion-body">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold text-sm mb-3" style="color: var(--gray-900);">
                                            Información de la Venta
                                        </h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span style="color: var(--gray-600);">Fecha:</span>
                                                <span class="font-medium" style="color: var(--gray-900);">
                                                    {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span style="color: var(--gray-600);">Total:</span>
                                                <span class="font-semibold" style="color: var(--success);">
                                                    S/ {{ number_format($venta->total, 2) }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span style="color: var(--gray-600);">Estado:</span>
                                                <span>
                                                    @if($venta->estado === 'pagado')
                                                        <span class="badge badge-success">Pagado</span>
                                                    @elseif($venta->estado === 'pendiente')
                                                        <span class="badge badge-warning">Pendiente</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ ucfirst($venta->estado) }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-center md:justify-end">
                                        <a href="{{ route('ventas.show', $venta->id) }}" 
                                           class="btn btn-primary">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver Detalle Completo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="icon-box mx-auto mb-4" style="width: 3rem; height: 3rem; background: var(--gray-100);">
                    <svg class="w-6 h-6" style="color: var(--gray-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold" style="color: var(--gray-900);">Sin historial de compras</h3>
                <p class="text-sm mt-1" style="color: var(--gray-500);">
                    Este cliente aún no ha realizado ninguna compra
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Filtros -->
<div id="filterModal" class="modal-backdrop">
    <div class="modal-content">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 1.25rem 1.5rem; border-radius: 0.75rem 0.75rem 0 0;">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtrar Compras
            </h2>
        </div>
        
        <!-- Body -->
        <div style="padding: 1.5rem;">
            <form method="GET" action="{{ route('ventas.historial', $cliente->id) }}">
                <div class="space-y-2 mb-4">
                    @php
                        $filtros = [
                            'today' => 'Hoy',
                            'yesterday' => 'Ayer',
                            'last_7_days' => 'Últimos 7 días',
                            'last_30_days' => 'Últimos 30 días',
                            'this_week' => 'Esta semana',
                            'last_week' => 'Semana pasada',
                            'this_month' => 'Este mes',
                            'last_month' => 'Mes pasado',
                            'this_year' => 'Este año',
                            'last_year' => 'El año pasado',
                            'custom' => 'Fecha personalizada'
                        ];
                    @endphp

                    @foreach ($filtros as $value => $label)
                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" 
                                   name="filter" 
                                   value="{{ $value }}" 
                                   class="w-4 h-4 text-primary focus:ring-primary"
                                   onchange="handleFilterChange(this)">
                            <span class="text-sm font-medium" style="color: var(--gray-700);">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- Custom Date Range -->
                <div id="customDateRange" style="display: none;">
                    <div style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem;">
                        <h4 class="text-xs font-semibold mb-3" style="color: var(--gray-700); text-transform: uppercase;">
                            Seleccionar rango de fechas
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: var(--gray-600);">
                                    Fecha inicial
                                </label>
                                <input type="date" 
                                       name="start_date" 
                                       class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                       style="border-color: var(--gray-300);">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: var(--gray-600);">
                                    Fecha final
                                </label>
                                <input type="date" 
                                       name="end_date" 
                                       class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                       style="border-color: var(--gray-300);">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3" style="padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                    <button type="button" onclick="closeFilterModal()" class="btn btn-secondary flex-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary flex-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Variables globales
let activeAccordion = null;

// Funciones del Accordion
function toggleAccordion(id) {
    const content = document.getElementById(id);
    const arrow = document.getElementById('arrow-' + id.split('-')[1]);
    
    // Si hay otro accordion abierto, cerrarlo
    if (activeAccordion && activeAccordion !== id) {
        const prevContent = document.getElementById(activeAccordion);
        const prevArrow = document.getElementById('arrow-' + activeAccordion.split('-')[1]);
        prevContent.classList.remove('active');
        prevArrow.style.transform = 'rotate(0deg)';
    }
    
    // Toggle del actual
    if (content.classList.contains('active')) {
        content.classList.remove('active');
        arrow.style.transform = 'rotate(0deg)';
        activeAccordion = null;
    } else {
        content.classList.add('active');
        arrow.style.transform = 'rotate(180deg)';
        activeAccordion = id;
    }
}

// Funciones del Modal
function openFilterModal() {
    const modal = document.getElementById('filterModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeFilterModal() {
    const modal = document.getElementById('filterModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Handler para cambio de filtro
function handleFilterChange(radio) {
    const customDateRange = document.getElementById('customDateRange');
    if (radio.value === 'custom') {
        customDateRange.style.display = 'block';
    } else {
        customDateRange.style.display = 'none';
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('filterModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeFilterModal();
        }
    });
    
    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeFilterModal();
        }
    });
    
    // Notification Toast
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-sm flex items-center gap-2 transform translate-x-full transition-transform duration-300';
        
        // Colores según tipo
        const colors = {
            success: 'background: var(--success);',
            error: 'background: var(--danger);',
            warning: 'background: var(--warning);',
            info: 'background: var(--info);'
        };
        
        toast.style.cssText = colors[type] || colors.info;
        
        // Icono
        const icon = document.createElement('svg');
        icon.className = 'w-5 h-5';
        icon.innerHTML = type === 'success' 
            ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            : '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        
        toast.innerHTML = icon.outerHTML + '<span>' + message + '</span>';
        document.body.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto-cerrar
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Verificar si hay filtros aplicados
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('filter')) {
        const filterLabels = {
            'today': 'Mostrando compras de hoy',
            'yesterday': 'Mostrando compras de ayer',
            'last_7_days': 'Mostrando últimos 7 días',
            'last_30_days': 'Mostrando últimos 30 días',
            'this_week': 'Mostrando esta semana',
            'last_week': 'Mostrando semana pasada',
            'this_month': 'Mostrando este mes',
            'last_month': 'Mostrando mes pasado',
            'this_year': 'Mostrando este año',
            'last_year': 'Mostrando año pasado',
            'custom': 'Mostrando rango personalizado'
        };
        
        const filterValue = urlParams.get('filter');
        if (filterLabels[filterValue]) {
            showToast(filterLabels[filterValue], 'success');
        }
    }
});

// Prevenir envío de formulario con campos vacíos en fecha personalizada
document.querySelector('form').addEventListener('submit', function(e) {
    const filterRadio = document.querySelector('input[name="filter"]:checked');
    if (filterRadio && filterRadio.value === 'custom') {
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;
        
        if (!startDate || !endDate) {
            e.preventDefault();
            alert('Por favor seleccione ambas fechas para el rango personalizado');
        }
    }
});
</script>
@endpush