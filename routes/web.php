<?php

use App\Http\Controllers\ProveedorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

    Route::get('/admin/ventas', fn() => view('admin.ventas.index'));
    Route::get('/admin/analisis', fn() => view('admin.analisis.index'));
    Route::get('/admin/compras', fn() => view('admin.compras.index'));
    Route::get('/admin/productos', fn() => view('admin.inventario.productos.index'));
    Route::get('/admin/categorias', fn() => view('admin.inventario.categorias'));
    Route::get('/admin/proveedores', fn() => view('admin.proveedores.index'));
    Route::get('/admin/usuarios', fn() => view('admin.usuarios.index'));
    Route::get('/admin/roles', fn() => view('admin.roles.index'));
    Route::get('/admin/clientes', fn() => view('admin.clientes.index'));
    Route::get('/admin/reportes', fn() => view('admin.reportes.index'));
    Route::get('/admin/perfil', fn() => view('admin.perfil.index'));

    Route::get('/admin/ajustes', fn() => view('admin.ajustes.index'));

    // Inventario
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
});
