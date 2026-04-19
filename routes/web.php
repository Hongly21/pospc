<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ChatbotController;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});


Route::middleware(['auth'])->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/pos', [OrderController::class, 'index'])->name('pos.index');
    Route::post('/pos/store', [OrderController::class, 'store'])->name('pos.store');
    Route::get('/pos/receipt/{id}', [OrderController::class, 'showReceipt'])->name('pos.receipt');
    Route::get('/pos/history', [OrderController::class, 'history'])->name('pos.history');

    Route::post('/customers/ajax', [CustomerController::class, 'storeAjax'])->name('customers.store.ajax');
    Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::get('/pos/customer-debt/{id}', [OrderController::class, 'checkCustomerDebt']);
    Route::post('/pos/order/{id}/pay-debt', [OrderController::class, 'payDebt'])->name('pos.payDebt');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Generate KHQR code for a given amount
    Route::post('/pos/khqr/generate', [OrderController::class, 'generateKhqr'])->name('pos.khqr.generate');

    // Poll to check if QR payment was received
    Route::post('/pos/khqr/check', [OrderController::class, 'checkKhqrPayment'])->name('pos.khqr.check');
});

Route::middleware(['auth', 'role:Admin,Manager'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products & Categories
    Route::resource('products', ProductController::class);
    Route::post('/products/update', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/delete', [ProductController::class, 'destroy'])->name('products.delete');

    Route::resource('categories', CategoryController::class);
    Route::post('/categories/update', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/delete', [CategoryController::class, 'destroy'])->name('categories.delete');

    // Full Customer Management
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/update', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/delete', [CustomerController::class, 'destroy'])->name('customers.delete');

    // Inventory Control
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/update', [InventoryController::class, 'update'])->name('inventory.update');
    Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');


    // Inventory Adjustments
    Route::get('/inventory/updatereorder', [InventoryController::class, 'updatereorder'])->name('inventory.updatereorder');

    // Suppliers, Purchases, and Expenses
    Route::resource('suppliers', SupplierController::class);
    Route::post('/suppliers/update', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::post('/suppliers/delete', [SupplierController::class, 'destroy'])->name('suppliers.delete');

    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/store', [PurchaseController::class, 'store'])->name('purchases.store');

    Route::resource('expenses', ExpenseController::class);
});

Route::middleware(['auth', 'role:Admin'])->group(function () {

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/delete', [UserController::class, 'destroy'])->name('users.delete');

    // User Approvals
    Route::get('/users/approve/{id}', [UserController::class, 'approve'])->name('users.approve');
    Route::get('/users/reject/{id}', [UserController::class, 'reject'])->name('users.reject');

    // System Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');

    // Global Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});




// Add this to routes/web.php
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'kh'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
