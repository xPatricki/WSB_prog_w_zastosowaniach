<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;

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

// User protected routes
Route::middleware('auth')->group(function () {
    Route::get('/my-books', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/books/{book}/borrow', [LoanController::class, 'borrow'])->name('books.borrow');
    Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update']);
});

// All admin routes in one group - accessible by admin and bookkeeper roles
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin home and dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin.home');
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Admin books management
    Route::get('/admin/books', [BookController::class, 'adminIndex'])->name('admin.books.index');
    Route::get('/admin/books/create', [BookController::class, 'create'])->name('admin.books.create');
    Route::post('/admin/books', [BookController::class, 'store'])->name('admin.books.store');
    Route::post('/admin/books/bulk-sync', [\App\Http\Controllers\Admin\BookManagementController::class, 'bulkSync'])->name('admin.books.bulkSync');
    Route::post('/admin/books/bulk-delete', [BookController::class, 'bulkDelete'])->name('admin.books.bulkDelete');
    Route::get('/admin/books/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
    Route::put('/admin/books/{book}', [BookController::class, 'update'])->name('admin.books.update');
    Route::delete('/admin/books/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');
    
    // Admin user management
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    
    // Admin loans management - using the AdminLoanController
    Route::get('/admin/loans', function () {
        return view('admin.loans.index');
    })->name('admin.loans.index');
    
    // Admin settings
    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');
});
