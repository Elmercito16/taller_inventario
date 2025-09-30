@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@push('meta')
<!-- SEO Meta Tags -->
<meta name="description" content="Gestión completa de proveedores del sistema de inventario">
<meta name="keywords" content="proveedores, suppliers, gestión, inventario, contactos">
<meta property="og:title" content="Proveedores - Sistema de Inventario">
<meta property="og:description" content="Administra y gestiona tus proveedores de manera eficiente">
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
        --success-dark: #059669;
        --warning: #f59e0b;
        --warning-soft: #f59e0b10;
        --warning-dark: #d97706;
        --danger: #ef4444;
        --danger-soft: #ef444410;
        --danger-dark: #dc2626;
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

    /* ========== Animaciones ========== */
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

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* ========== Base Styles ========== */
    * {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ========== Componentes ========== */
    .page-container {
        animation: fadeIn 0.3s ease-out;
    }

    .header-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 1.5rem;
        animation: slideUp 0.4s ease-out;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        border-color: var(--primary-20);
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.15s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.25s; }

    /* ========== Table Styles ========== */
    .table-container {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
    }

    .table-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 1.25rem 1.5rem;
        color: white;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .custom-table thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .custom-table thead th {
        padding: 0.875rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .custom-table tbody tr {
        border-bottom: 1px solid var(--gray-100);
        transition: all 0.2s ease;
        position: relative;
    }

    .custom-table tbody tr:hover {
        background: var(--primary-10);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .custom-table tbody tr:hover td {
        color: var(--gray-900);
    }

    .custom-table tbody td {
        padding: 1rem;
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .custom-table tbody tr:last-child {
        border-bottom: none;
    }

    /* ========== Buttons ========== */
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
        user-select: none;
        position: relative;
        overflow: hidden;
        border: 1px solid transparent;
    }

    .btn:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 2px 4px rgba(33, 135, 134, 0.2);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(33, 135, 134, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
    }

    .btn-warning:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
    }

    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    .btn-sm {
        padding: 0.5rem 0.875rem;
        font-size: 0.8125rem;
    }

    /* ========== Icons ========== */
    .icon-box {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-box-lg {
        width: 3rem;
        height: 3rem;
    }

    /* ========== Badge ========== */
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

    /* ========== Search Bar ========== */
    .search-container {
        position: relative;
        max-width: 400px;
    }

    .search-input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.75rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-20);
    }

    .search-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }

    /* ========== Empty State ========== */
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        color: var(--gray-500);
    }

    .empty-state-icon {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        background: var(--gray-100);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ========== Action Buttons Container ========== */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* ========== Tooltips ========== */
    .tooltip {
        position: relative;
    }

    .tooltip:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: var(--gray-800);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 10;
        margin-bottom: 0.25rem;
    }

    /* ========== Loading Skeleton ========== */
    .skeleton {
        background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: var(--radius);
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .custom-table {
            font-size: 0.8125rem;
        }

        .custom-table thead th,
        .custom-table tbody td {
            padding: 0.75rem 0.5rem;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .action-buttons .btn {
            width: 100%;
        }
    }

    @media (max-width: 640px) {
        .header-card {
            padding: 1rem;
        }

        .table-container {
            border-radius: 0;
            border-left: 0;
            border-right: 0;
        }
    }

    /* ========== Print Styles ========== */
    @media print {
        .no-print {
            display: none !important;
        }

        .table-container {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>
@endpush

@section('content')
<div class="page-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    <!-- Breadcrumbs -->
    <nav class="breadcrumb mb-6" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span aria-current="page" class="font-medium text-gray-900">Proveedores</span>
    </nav>

    <!-- Header Card -->
    <div class="header-card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="icon-box icon-box-lg" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestión de Proveedores</h1>
                    <p class="text-sm text-gray-600 mt-1">Administra los proveedores del sistema</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="search-container">
                    <svg class="search-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           id="searchInput" 
                           class="search-input" 
                           placeholder="Buscar proveedor...">
                </div>
                
                <a href="{{ route('proveedores.create') }}" class="btn btn-success no-print">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Proveedor
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Total Proveedores</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $proveedores->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Registrados</p>
                </div>
                <div class="icon-box" style="background: var(--info-soft);">
                    <svg class="w-5 h-5" style="color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Activos</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $proveedores->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Este mes</p>
                </div>
                <div class="icon-box" style="background: var(--success-soft);">
                    <svg class="w-5 h-5" style="color: var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Nuevos</p>
                    <p class="text-2xl font-bold text-primary mt-1" style="color: var(--primary);">{{ $proveedores->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Último mes</p>
                </div>
                <div class="icon-box" style="background: var(--primary-10);">
                    <svg class="w-5 h-5" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Ciudades</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $proveedores->pluck('direccion')->unique()->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Ubicaciones</p>
                </div>
                <div class="icon-box" style="background: rgba(147, 51, 234, 0.1);">
                    <svg class="w-5 h-5" style="color: rgb(147, 51, 234);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-header">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="icon-box" style="background: rgba(255, 255, 255, 0.2);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Lista de Proveedores</h2>
                        <p class="text-sm text-white opacity-90">{{ $proveedores->count() }} proveedores registrados</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="exportTable()" class="btn btn-sm bg-white bg-opacity-20 text-white hover:bg-opacity-30 no-print">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar
                    </button>
                </div>
            </div>
        </div>

        @if($proveedores->count() > 0)
            <div class="overflow-x-auto">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary focus:ring-primary">
                            </th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email/Contacto</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="proveedoresTable">
                        @foreach($proveedores as $proveedor)
                        <tr data-proveedor="{{ strtolower($proveedor->nombre) }}">
                            <td>
                                <input type="checkbox" class="proveedor-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                            </td>
                            <td>
                                <span class="font-medium text-gray-900">#{{ str_pad($proveedor->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="icon-box" style="background: var(--primary-10); width: 32px; height: 32px;">
                                        <span class="text-sm font-bold" style="color: var(--primary);">{{ substr($proveedor->nombre, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $proveedor->nombre }}</p>
                                        <p class="text-xs text-gray-500">Desde {{ $proveedor->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>{{ $proveedor->telefono ?? 'No registrado' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $proveedor->contacto ?? 'No registrado' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $proveedor->direccion ?? 'No registrado' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Activo
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons justify-center">
                                    <a href="{{ route('proveedores.show', $proveedor->id) }}" 
                                       class="btn btn-sm btn-primary tooltip"
                                       data-tooltip="Ver detalles">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('proveedores.edit', $proveedor->id) }}" 
                                       class="btn btn-sm btn-warning tooltip"
                                       data-tooltip="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('proveedores.destroy', $proveedor->id) }}" 
                                          method="POST" 
                                          class="delete-form"
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="btn btn-sm btn-danger tooltip"
                                                data-tooltip="Eliminar"
                                                onclick="confirmDelete(this)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No hay proveedores registrados</h3>
                <p class="text-sm text-gray-500 mt-1">Comienza agregando tu primer proveedor</p>
                <a href="{{ route('proveedores.create') }}" class="btn btn-primary mt-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Primer Proveedor
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed bottom-4 right-4 z-50"></div>

@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#proveedoresTable tr');
    
    searchInput?.addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Select all checkboxes
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.proveedor-checkbox');
    
    selectAll?.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Individual checkbox handling
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        });
    });
    
    // Toast notification function
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        
        const colors = {
            success: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
            warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            info: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'
        };
        
        toast.className = 'flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white mb-3 transform translate-x-full transition-transform duration-300';
        toast.style.background = colors[type] || colors.info;
        
        const icon = type === 'success' 
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        
        toast.innerHTML = icon + '<span class="font-medium">' + message + '</span>';
        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Export table functionality
    window.exportTable = function() {
        showToast('Exportación iniciada...', 'info');
        // Aquí puedes agregar la lógica real de exportación
    };
    
    // Delete confirmation
    window.confirmDelete = function(button) {
        const form = button.closest('form');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#218786',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    };
    
    // Success message from session
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar',
            timer: 3000,
            timerProgressBar: true
        });
    @endif
    
    // Error message from session
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar'
        });
    @endif
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput?.focus();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('keyup'));
        }
    });
    
    // Add hover effect animations
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-2px) scale(1)';
        });
    });
});
</script>
@endpush