@extends('layouts.app')

@section('title', 'Reporte de inventario')

@section('content')
    @include('_partials.reportes.inventario-table', [
        'filters' => $filters,
        'kpis' => $kpis,
        'rows' => $rows,
        'categories' => $categories,
        'suppliers' => $suppliers,
    ])
@endsection
