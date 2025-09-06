@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agregar cantidad a {{ $repuesto->nombre }}</h1>

    <form action="{{ route('repuestos.update', $repuesto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad a agregar</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Stock</button>
    </form>
</div>
@endsection
