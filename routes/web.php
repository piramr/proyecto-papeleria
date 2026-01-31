<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentasController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

    // Rutas del módulo de Ventas
    Route::resource('ventas', VentasController::class);
    Route::resource('admin/ventas', VentasController::class);
    Route::get('ventas/{factura}/print', [VentasController::class, 'print'])->name('ventas.print');
    Route::get('admin/ventas/{factura}/print', [VentasController::class, 'print']);
    Route::get('api/productos', [VentasController::class, 'getProductos'])->name('api.productos');
    Route::get('api/cliente/{cedula}', [VentasController::class, 'getClienteByCedula'])->name('api.cliente');
    Route::get('/admin/analisis', fn() => view('admin.analisis.index'));
    Route::get('/admin/compras', fn() => view('admin.compras.index'));
    Route::get('/admin/productos', fn() => view('admin.inventario.productos'));
    Route::get('/admin/categorias', fn() => view('admin.inventario.categorias'));
    Route::get('/admin/proveedores', fn() => view('admin.proveedores.index'));
    Route::get('/admin/usuarios', fn() => view('admin.usuarios.index'));
    Route::get('/admin/roles', fn() => view('admin.roles.index'));
    Route::get('/admin/clientes', fn() => view('admin.clientes.index'));
    Route::get('/admin/reportes', fn() => view('admin.reportes.index'));
    Route::get('/admin/perfil', fn() => view('admin.perfil.index'));

    Route::get('/admin/ajustes', fn() => view('admin.ajustes.index'));
});
