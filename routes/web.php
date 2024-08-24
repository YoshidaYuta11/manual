<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('products.index');
    } else {
        return redirect()->route('login');
    }
});

Auth::routes();

Route::group(['prefix' => 'products', 'middleware' => 'auth'], function () {
    // Index
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/ajax', [ProductController::class, 'index'])->name('products.ajax.index');

    
    // Create
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');

    // Show
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Edit
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');

    // Destroy
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
