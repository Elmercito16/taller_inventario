@extends('layouts.app')

@section('title', 'Agregar stock')
@section('page-title', 'Agregar stock')
@section('page-description', 'Suma unidades al inventario de un producto')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-lg font-semibold text-gray-900 truncate">{{ $repuesto->nombre }}</h2>
                <p class="text-sm text-gray-500">{{ $repuesto->codigo }} · Stock actual:
                    <span class="font-semibold {{ $repuesto->tieneStockBajo() ? 'text-red-600' : 'text-gray-900' }}">{{ $repuesto->stock_formateado }}</span>
                </p>
            </div>
        </div>

        <form action="{{ route('repuestos.updateCantidad', $repuesto->id) }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="cantidad" class="form-label">Cantidad a agregar ({{ $repuesto->unidad_nombre }})</label>
                <input type="number" id="cantidad" name="cantidad"
                       min="{{ $repuesto->permiteDecimales() ? '0.001' : '1' }}"
                       step="{{ $repuesto->permiteDecimales() ? '0.001' : '1' }}"
                       value="{{ old('cantidad', 1) }}"
                       class="form-input @error('cantidad') border-red-400 @enderror" required autofocus>
                @error('cantidad')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('repuestos.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Agregar stock</button>
            </div>
        </form>
    </div>
</div>
@endsection
