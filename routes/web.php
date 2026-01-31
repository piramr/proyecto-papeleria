<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// ✅ Ruta universal: redirige según rol
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
    'role:Admin|Empleado', // 👈 Permitimos ambos roles
])->prefix('admin')->name('admin.')->group(function () {

    // Módulos compartidos
    Route::get('/ventas', fn() => view('admin.ventas.index'))->name('ventas');
    Route::get('/analisis', fn() => view('admin.analisis.index'))->name('analisis');
    Route::get('/compras', fn() => view('admin.compras.index'))->name('compras');
    Route::get('/productos', fn() => view('admin.inventario.productos'))->name('productos');
    Route::get('/categorias', fn() => view('admin.inventario.categorias'))->name('categorias');
    Route::get('/proveedores', fn() => view('admin.proveedores.index'))->name('proveedores');
    Route::resource('clientes', \App\Http\Controllers\Admin\ClienteController::class);
    Route::get('/reportes', fn() => view('admin.reportes.index'))->name('reportes');

    // ===================== SOLO ADMIN =====================
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
        Route::get('/perfil', fn() => view('admin.perfil.index'))->name('perfil');
        Route::get('/ajustes', fn() => view('admin.ajustes.index'))->name('ajustes');

        // Gestión de Usuarios (Exclusivo Admin)
        Route::resource('usuarios', \App\Http\Controllers\Admin\UserController::class);
        Route::post('usuarios/{usuario}/unlock', [\App\Http\Controllers\Admin\UserController::class, 'unlock'])->name('usuarios.unlock');
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['show']);
    });
});


// ===================== EMPLEADO =====================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:Empleado',
])->prefix('empleado')->name('empleado.')->group(function () {

    Route::get('/dashboard', fn() => view('empleado.dashboard'))->name('dashboard');

    // Aquí pones SOLO lo que el empleado puede ver
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

    // Solo auditoría / lectura
    Route::get('/auditoria', fn() => view('auditoria.index'))->name('auditoria');
    Route::get('/ajustes', fn() => view('auditor.settings'))->name('ajustes');
])->group(function () {
    Route::get('admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

    Route::get('/admin/ventas', fn() => view('admin.ventas.index'));
    Route::get('/admin/analisis', fn() => view('admin.analisis.index'));
    Route::get('/admin/compras', fn() => view('admin.compras.index'));
    Route::get('/admin/productos', [ProductoController::class, 'index'])->name('admin.productos');
    Route::get('/admin/categorias', fn() => view('admin.inventario.categorias.index'))->name('admin.categorias');
    Route::get('/admin/proveedores', fn() => view('admin.proveedores.index'))->name('admin.proveedores.index');
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
    Route::get('/productos/export-pdf', [ProductoController::class, 'exportPdf'])->name('productos.export-pdf');
    Route::get('/productos/export-excel', [ProductoController::class, 'exportExcel'])->name('productos.export-excel');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    Route::get('/proveedores/export-pdf', [ProveedorController::class, 'exportPdf'])->name('proveedores.export-pdf');
    Route::get('/proveedores/export-excel', [ProveedorController::class, 'exportExcel'])->name('proveedores.export-excel');

    Route::get('/pdf', [ProductoController::class, 'exportPdf']);
});
