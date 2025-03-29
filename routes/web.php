<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AuthController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/my-books', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/books/{book}/borrow', [LoanController::class, 'borrow'])->name('books.borrow');
    Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    
    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/books', [BookController::class, 'adminIndex'])->name('admin.books.index');
        Route::get('/admin/books/create', [BookController::class, 'create'])->name('admin.books.create');
        Route::post('/admin/books', [BookController::class, 'store'])->name('admin.books.store');
        Route::get('/admin/books/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
        Route::put('/admin/books/{book}', [BookController::class, 'update'])->name('admin.books.update');
        Route::delete('/admin/books/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');
    });
});

