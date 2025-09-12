<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirigir raíz al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');
    
    // Perfil - accesible para todos los usuarios autenticados
    Route::get('/perfil', function () { 
        return view('minformacion'); 
    })->name('profile.index');
    
    // Cursos - para profesores y estudiantes
    Route::middleware(['role:teacher,student,admin'])->group(function () {
        Route::get('/cursos', function () { 
            return view('courses.index'); 
        })->name('courses.index');
    });
    
    // Facturación - solo para roles específicos
    Route::middleware(['role:billing,admin'])->group(function () {
        Route::get('/facturacion', function () { 
            return view('billing.index'); 
        })->name('billing.index');
    });
    
    // Administración - solo para admins
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', function () { 
            return view('admin.index'); 
        })->name('admin.index');
    });
    
    // Ajustes - accesible para todos
    Route::get('/ajustes', function () { 
        return view('settings.index'); 
    })->name('settings.index');
});
