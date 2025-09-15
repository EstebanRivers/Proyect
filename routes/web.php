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
    Route::get('/mi-informacion', function () { 
        return view('minformacion'); 
    })->name('profile.index');
    
    // Cursos - para profesores y estudiantes
    Route::middleware(['role:docente,alumno,admin'])->group(function () {
        Route::get('/cursos', [App\Http\Controllers\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/topics/{topic}', [App\Http\Controllers\CourseController::class, 'showTopic'])->name('courses.topics.show');
        Route::post('/courses/{course}/enroll', [App\Http\Controllers\CourseController::class, 'enroll'])->name('courses.enroll');
        Route::post('/courses/{course}/unenroll', [App\Http\Controllers\CourseController::class, 'unenroll'])->name('courses.unenroll');
        Route::post('/courses/topics/{topic}/complete', [App\Http\Controllers\CourseController::class, 'completeTopic'])->name('courses.topics.complete');
    });
    
    // Gestión de cursos - solo para admins y docentes
    Route::middleware(['role:admin,docente'])->group(function () {
        Route::get('/cursos/crear', [App\Http\Controllers\CourseController::class, 'create'])->name('courses.create');
        Route::post('/cursos', [App\Http\Controllers\CourseController::class, 'store'])->name('courses.store');
    // Gestión de temas y contenido (TODO en CourseController)
    Route::get('/cursos/{curso}/temas', [App\Http\Controllers\CourseController::class, 'manageTopics'])->name('courses.manage-topics');
    Route::post('/cursos/{curso}/temas', [App\Http\Controllers\CourseController::class, 'storeTopic'])->name('courses.topics.store');
    Route::get('/cursos/{curso}/contenido/crear', [App\Http\Controllers\CourseController::class, 'createContent'])->name('courses.content.create');
    Route::post('/cursos/{curso}/contenido', [App\Http\Controllers\CourseController::class, 'storeContent'])->name('courses.content.store');
    // Mantener builder por compatibilidad, pero redirige
    Route::get('/cursos/{curso}/builder', [App\Http\Controllers\CourseController::class, 'builder'])->name('courses.builder');
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
