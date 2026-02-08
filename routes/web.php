<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\AlertasController;
use App\Http\Controllers\ReportesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\Admin\AjusteController;
use App\Models\Categoria;
use App\Models\Proveedor;

Route::get('/', fn() => view('welcome'));

Route::get('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/keep-alive', fn() => response()->noContent())->name('keep-alive');
    Route::get('two-factor-auth', [\App\Http\Controllers\TwoFactorController::class, 'index'])->name('two-factor.index');
    Route::post('two-factor-auth', [\App\Http\Controllers\TwoFactorController::class, 'store'])->name('two-factor.store');
    Route::get('two-factor-auth/resend', [\App\Http\Controllers\TwoFactorController::class, 'resend'])->name('two-factor.resend');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('Admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('Empleado')) {
        return redirect()->route('empleado.dashboard');
    }

    if ($user->hasRole('Auditor')) {
        return redirect()->route('auditor.dashboard');
    }

    return view('auth.pending');
})->name('dashboard');


// ===================== PANEL COMPARTIDO (Admin + Empleado) =====================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:Admin|Empleado',
])->prefix('admin')->name('admin.')->group(function () {

    // Módulos compartidos (vistas)
    Route::get('/ventas', fn() => view('admin.ventas.index'))->name('ventas');
    Route::get('/analisis', fn() => view('admin.analisis.index'))->name('analisis');
    Route::get('/productos', fn() => view('admin.inventario.productos.index', [
        'categorias' => Categoria::all(),
        'proveedores' => Proveedor::all(),
    ]))->name('productos');
    Route::get('/categorias', fn() => view('admin.inventario.categorias.index'))->name('categorias');
    Route::get('/proveedores', fn() => view('admin.proveedores.index'))->name('proveedores');
    Route::resource('clientes', \App\Http\Controllers\Admin\ClienteController::class);
    
    // ✅ Rutas para reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReportesController::class, 'index'])->name('index');
        Route::get('ventas/pdf', [ReportesController::class, 'ventasPdf'])->name('ventas.pdf');
        Route::get('ventas/excel', [ReportesController::class, 'ventasExcel'])->name('ventas.excel');
        Route::get('compras/pdf', [ReportesController::class, 'comprasPdf'])->name('compras.pdf');
        Route::get('compras/excel', [ReportesController::class, 'comprasExcel'])->name('compras.excel');
        Route::get('ganancias/excel', [ReportesController::class, 'gananciasExcel'])->name('ganancias.excel');
        Route::get('inventario/excel', [ReportesController::class, 'inventarioExcel'])->name('inventario.excel');
    });

    // ✅ Rutas para compras (módulo)
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/', [CompraController::class, 'index'])->name('index');
        Route::get('crear', [CompraController::class, 'create'])->name('create');
        Route::post('/', [CompraController::class, 'store'])->name('store');
        Route::get('{compra}', [CompraController::class, 'show'])->name('show');
        Route::get('{compra}/editar', [CompraController::class, 'edit'])->name('edit');
        Route::put('{compra}', [CompraController::class, 'update'])->name('update');
        Route::post('{compra}/recibir', [CompraController::class, 'recibir'])->name('recibir');
        Route::post('{compra}/cancelar', [CompraController::class, 'cancelar'])->name('cancelar');
        Route::get('productos-proveedor/{proveedorRuc}', [CompraController::class, 'obtenerProductosProveedor'])->name('productos-proveedor');
        Route::get('{compra}/factura', [CompraController::class, 'generarFactura'])->name('factura');
    });

    // ✅ Rutas para ventas (módulo)
    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/', [VentasController::class, 'index'])->name('index');
        Route::get('/create', [VentasController::class, 'create'])->name('create');
        Route::post('/', [VentasController::class, 'store'])->name('store');
        Route::get('/{factura}', [VentasController::class, 'show'])->name('show');
        Route::get('/{factura}/print', [VentasController::class, 'print'])->name('print');
        Route::delete('/{factura}', [VentasController::class, 'destroy'])->name('destroy');
        Route::get('api/cliente/{cedula}', [VentasController::class, 'getClienteByCedula'])->name('api.cliente');
    });

    // APIs auxiliares para ventas
    Route::get('api/productos', [VentasController::class, 'getProductos'])->name('api.productos');

    // ✅ Rutas para alertas
    Route::get('alertas/stock-bajo', [AlertasController::class, 'stockBajo'])->name('alertas.stock-bajo');
    Route::get('api/alertas/stock-bajo', [AlertasController::class, 'apiStockBajo'])->name('api.alertas.stock-bajo');
    Route::get('api/todas-alertas', [AlertasController::class, 'apiTodasAlertas'])->name('api.todas-alertas');

    // ===================== SOLO ADMIN =====================
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
        Route::get('/perfil', fn() => view('admin.perfil.index'))->name('perfil');
        Route::get('/ajustes', [AjusteController::class, 'index'])->name('ajustes');
        Route::put('/ajustes', [AjusteController::class, 'update'])->name('ajustes.update');

        // Gestión de Usuarios (Exclusivo Admin)
        Route::resource('usuarios', \App\Http\Controllers\Admin\UserController::class);
        Route::post('usuarios/{usuario}/unlock', [\App\Http\Controllers\Admin\UserController::class, 'unlock'])->name('usuarios.unlock');
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['show']);
    });

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
    Route::get('/productos/export-pdf', [ProductoController::class, 'exportPdf'])->name('productos.export-pdf');
    Route::get('/productos/export-excel', [ProductoController::class, 'exportExcel'])->name('productos.export-excel');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    Route::get('/proveedores/export-pdf', [ProveedorController::class, 'exportPdf'])->name('proveedores.export-pdf');
    Route::get('/proveedores/export-excel', [ProveedorController::class, 'exportExcel'])->name('proveedores.export-excel');

    Route::get('/pdf', [ProductoController::class, 'exportPdf']);
});


// ===================== EMPLEADO =====================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:Empleado',
])->prefix('empleado')->name('empleado.')->group(function () {
    Route::get('/dashboard', fn() => view('empleado.dashboard'))->name('dashboard');
    Route::get('/ventas', fn() => view('empleado.ventas.index'))->name('ventas');
});


// ===================== AUDITOR =====================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:Auditor',
])->prefix('auditor')->name('auditor.')->group(function () {
    Route::get('/dashboard', fn() => view('auditor.dashboard'))->name('dashboard');
    Route::get('/auditoria', fn() => view('auditor.auditoria'))->name('auditoria');
    Route::get('/logs', fn() => view('auditor.logs'))->name('logs');
    Route::get('/ajustes', fn() => view('auditor.ajustes'))->name('ajustes');
});
