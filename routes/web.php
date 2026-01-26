<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
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
    Route::get('/admin/productos', [ProductoController::class, 'index'])->name('admin.productos');
    Route::get('/admin/categorias', fn() => view('admin.inventario.categorias.index'))->name('admin.categorias');
    Route::get('/admin/proveedores', fn() => view('admin.proveedores.index'));
    Route::get('/admin/usuarios', fn() => view('admin.usuarios.index'));
    Route::get('/admin/roles', fn() => view('admin.roles.index'));
    Route::get('/admin/clientes', fn() => view('admin.clientes.index'));
    Route::get('/admin/reportes', fn() => view('admin.reportes.index'));
    Route::get('/admin/perfil', fn() => view('admin.perfil.index'));

    Route::get('/admin/ajustes', fn() => view('admin.ajustes.index'));

    // Inventario
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::get('/proveedores/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
    Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    Route::get('/proveedores/datatables', [ProveedorController::class, 'datatables'])->name('proveedores.datatables');
    
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::get('/categorias/{categoria}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    Route::get('/categorias/datatables', [CategoriaController::class, 'datatables'])->name('categorias.datatables');
    
    Route::get('/productos/datatables', [ProductoController::class, 'datatables'])->name('productos.datatables');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});

