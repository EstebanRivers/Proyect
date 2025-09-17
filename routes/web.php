<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TopicsController;
use App\Http\Controllers\ActivitiesController;

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
    Route::get('/mi-informacion', function () { 
        return view('minformacion'); 
    })->name('profile.index');
    
    // Cursos - para anfitriones y estudiantes
    Route::middleware(['role:docente,alumno,admin,anfitrion'])->group(function () {
        Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
    });
    
    // Gestión de cursos - solo para admins y docentes
    Route::middleware(['role:admin,docente'])->group(function () {
        Route::get('/cursos/crear', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/cursos', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/cursos/{course}/temas/crear', [TopicsController::class, 'create'])->name('courses.topics.create'); // <--- AÑADIDO Y CORREGIDO

        Route::post('/temas', [TopicsController::class, 'store'])->name('topics.store');
        Route::get('/actividades', [ActivitiesController::class, 'store'])->name('activities.create');
    });
    
    // Facturación - solo para roles específicos
    Route::middleware(['role:alumno,admin'])->group(function () {
        Route::get('/facturacion', function () { 
            return view('billing.index'); 
        })->name('billing.index');
    });
    
    // Administración - solo para admins
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', function () { 
            return view('admin.index'); 
        })->name('admin.index');
        
        // Gestión de roles
        Route::prefix('admin/roles')->name('admin.roles.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
            Route::get('/users', [App\Http\Controllers\Admin\RoleController::class, 'users'])->name('users');
            Route::post('/assign', [App\Http\Controllers\Admin\RoleController::class, 'assignRole'])->name('assign');
            Route::post('/remove', [App\Http\Controllers\Admin\RoleController::class, 'removeRole'])->name('remove');
        });
    });
    
    // Ajustes - accesible para todos
    Route::get('/ajustes', function () { 
        return view('settings.index'); 
    })->name('settings.index');
});
