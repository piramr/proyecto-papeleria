<?php

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
});
