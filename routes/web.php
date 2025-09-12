<?php

// routes/web.php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/perfil', function () { return view('minformacion'); })->name('profile.index');
Route::get('/cursos', function () { return view('welcome'); })->name('courses.index');     // ejemplo
Route::get('/facturacion', function () { return view('welcome'); })->name('billing.index');
Route::get('/admin', function () { return view('welcome'); })->name('admin.index');
Route::get('/ajustes', function () { return view('welcome'); })->name('settings.index');

Route::get('/logout', function () {
    // ejemplo simple: redirigir a home
    return redirect()->route('home');
})->name('logout');
