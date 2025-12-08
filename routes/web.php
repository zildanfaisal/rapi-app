<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\TransactionController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users CRUD protected by permissions
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.update')->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.update')->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');

    // Roles management protected by permissions
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:roles.create')->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:roles.view')->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.update')->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.update')->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('roles.destroy');

    // Permissions management protected by permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permissions.view')->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->middleware('permission:permissions.create')->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:permissions.create')->name('permissions.store');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->middleware('permission:permissions.view')->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->middleware('permission:permissions.update')->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:permissions.update')->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.delete')->name('permissions.destroy');

    // Customer Routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Product Routes
    Route::get('/products', [ProductController::class, 'index'])->middleware('permission:products.view')->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->middleware('permission:products.create')->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Product Batch Routes
    Route::get('/product-batches', [ProductBatchController::class, 'index'])->middleware('permission:product-batches.view')->name('product-batches.index');
    Route::get('/product-batches/create', [ProductBatchController::class, 'create'])->middleware('permission:product-batches.create')->name('product-batches.create');
    Route::post('/product-batches', [ProductBatchController::class, 'store'])->name('product-batches.store');
    Route::get('/product-batches/{productBatch}/edit', [ProductBatchController::class, 'edit'])->name('product-batches.edit');
    Route::put('/product-batches/{productBatch}', [ProductBatchController::class, 'update'])->name('product-batches.update');
    Route::delete('/product-batches/{productBatch}', [ProductBatchController::class, 'destroy'])->name('product-batches.destroy');
    Route::get('/product-batches/report', [ProductBatchController::class, 'report'])
    ->name('product-batches.report');

    // Penjualan Routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    // Setor Penjualan (place BEFORE catch-all invoice routes)
    Route::get('/invoices/setor', [InvoiceController::class, 'indexSetor'])->name('invoices.setor');
    Route::get('/invoices/{invoice}/setor', [InvoiceController::class, 'editSetor'])->name('invoices.setor.edit');
    Route::post('/invoices/{invoice}/setor', [InvoiceController::class, 'updateSetor'])->name('invoices.setor.update');

    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat-jalan.index');
    Route::get('/surat-jalan/create', [SuratJalanController::class, 'create'])->name('surat-jalan.create');
    Route::post('/surat-jalan', [SuratJalanController::class, 'store'])->name('surat-jalan.store');
    Route::get('/surat-jalan/{suratJalan}', [SuratJalanController::class, 'show'])->name('surat-jalan.show');
    Route::get('/surat-jalan/{suratJalan}/edit', [SuratJalanController::class, 'edit'])->name('surat-jalan.edit');
    Route::put('/surat-jalan/{suratJalan}', [SuratJalanController::class, 'update'])->name('surat-jalan.update');
    Route::get('/surat-jalan/{suratJalan}/pdf', [SuratJalanController::class, 'pdf'])->name('surat-jalan.pdf');
    Route::delete('/surat-jalan/{suratJalan}', [SuratJalanController::class, 'destroy'])->name('surat-jalan.destroy');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
});

require __DIR__.'/auth.php';
