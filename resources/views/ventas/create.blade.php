@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-[#218786]">Dashboard</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('ventas.index') }}" class="hover:text-[#218786]">Ventas</a>
    <span class="text-gray-400">/</span>
    <span class="font-medium text-gray-900">Nueva</span>
</nav>
@endsection

@push('styles')
<style>
    .bg-brand { background-color: #218786; }
    .text-brand { color: #218786; }
    .border-brand { border-color: #218786; }
    .ring-brand:focus { --tw-ring-color: #218786; }
    .hover\:bg-brand-dark:hover { background-color: #1a6d6c; }
    
    /* Animación suave para modales */
    .modal-enter { animation: modalIn 0.2s ease-out forwards; }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #218786; }
    
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<!-- INICIALIZAMOS ALPINE JS AQUÍ -->
<div class="h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-6" x-data="posSystem()">
    
    <!-- 1. COLUMNA IZQUIERDA: LISTA DEL CARRITO -->
    @include('ventas.partials.cart-list')

    <!-- 2. COLUMNA DERECHA: RESUMEN Y CLIENTE -->
    @include('ventas.partials.checkout-sidebar')

    <!-- 3. VENTANAS MODALES -->
    @include('ventas.partials.modals')

</div>

@php
    // Preparamos los datos para JS
    $repuestos_js = $repuestos->map(fn($r) => [
        'id' => (int)$r->id, 
        'nombre' => (string)$r->nombre, 
        'categoria' => $r->categoria ? (string)$r->categoria->nombre : 'General', 
        'precio_unitario' => (float)($r->precio_unitario ?? 0), 
        'stock' => (int)($r->cantidad ?? 0)
    ])->values()->toArray();

    $categorias = $repuestos->pluck('categoria')->whereNotNull()->unique()->values()->map(fn($c) => $c->nombre)->toArray();
@endphp

@push('scripts')
<script>
function posSystem() {
    return {
        // Datos iniciales
        products: @json($repuestos_js),
        categories: @json($categorias),
        items: [],
        selectedClient: null,
        
        // Estados de UI
        showProductModal: false,
        showClientModal: false,
        isSubmitting: false,
        
        // Filtros y Búsquedas
        productSearch: '',
        selectedCategory: '',
        clientSearchQuery: '',
        clientResults: [],
        isSearchingClient: false,
        
        // Formulario Nuevo Cliente
        newClient: { dni: '', nombre: '', direccion: '', telefono: '' },

        // --- LÓGICA DE PRODUCTOS ---
        get filteredProducts() {
            return this.products.filter(p => {
                const matchSearch = !this.productSearch || p.nombre.toLowerCase().includes(this.productSearch.toLowerCase());
                const matchCat = !this.selectedCategory || p.categoria === this.selectedCategory;
                return matchSearch && matchCat;
            });
        },
        
        get cartTotal() { return this.items.reduce((s, i) => s + (i.precio * i.cantidad), 0); },

        addToCart(p) {
            if(p.stock <= 0) return this.toast('Sin stock', 'error');
            let item = this.items.find(i => i.id === p.id);
            if(item) {
                if(item.cantidad < p.stock) { item.cantidad++; this.toast('Cantidad +1'); }
                else this.toast('Stock máximo alcanzado', 'warning');
            } else {
                this.items.push({ ...p, cantidad: 1, precio: p.precio_unitario });
                this.toast('Producto agregado');
                // Opcional: this.showProductModal = false; 
            }
        },

        updateQty(idx, change) {
            let item = this.items[idx];
            let original = this.products.find(p => p.id === item.id);
            let n = item.cantidad + change;
            
            if(n > original.stock) return this.toast('Stock insuficiente', 'warning');
            if(n > 0) item.cantidad = n;
            else this.removeItem(idx);
        },
        
        removeItem(idx) { this.items.splice(idx, 1); },

        // --- LÓGICA DE CLIENTES ---
        async searchClients() {
            if(this.clientSearchQuery.length < 2) { this.clientResults = []; return; }
            this.isSearchingClient = true;
            try {
                let res = await fetch(`/clientes/search?q=${this.clientSearchQuery}`);
                this.clientResults = await res.json();
            } catch(e) {}
            this.isSearchingClient = false;
        },

        selectClient(c) {
            this.selectedClient = c;
            this.showClientModal = false;
            this.toast('Cliente asignado');
        },

        async createClient() {
            if(!this.newClient.dni || !this.newClient.nombre) return this.toast('DNI y Nombre obligatorios', 'error');
            try {
                let res = await fetch("{{ route('clientes.storeQuick') }}", {
                    method: "POST", headers: {"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}"},
                    body: JSON.stringify(this.newClient)
                });
                let data = await res.json();
                if(data.success) {
                    this.selectClient(data.cliente);
                    this.newClient = {dni:'', nombre:'', direccion:'', telefono:''};
                } else {
                    let msg = data.message || 'Error al guardar';
                    if(data.errors) msg = Object.values(data.errors).flat()[0];
                    this.toast(msg, 'error');
                }
            } catch(e) { this.toast('Error del servidor', 'error'); }
        },

        // --- FINALIZAR VENTA ---
        submitSale() {
            if(this.items.length === 0) return this.toast('Carrito vacío', 'error');
            
            Swal.fire({
                title: 'Confirmar Venta',
                text: `Total: S/ ${this.cartTotal.toFixed(2)}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#218786',
                confirmButtonText: 'Sí, procesar'
            }).then((res) => {
                if(res.isConfirmed) {
                    this.isSubmitting = true;
                    document.getElementById('ventaForm').submit();
                }
            });
        },

        toast(msg, type='success') {
            const colors = { success:'#218786', error:'#ef4444', warning:'#f59e0b' };
            Swal.fire({ toast: true, position: 'top-end', icon: type, title: msg, showConfirmButton: false, timer: 2000, iconColor: colors[type] });
        }
    }
}
</script>
@endpush
@endsection