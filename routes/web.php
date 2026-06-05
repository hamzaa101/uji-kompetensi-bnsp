<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SimulationController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Apoteker\ApotekerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\SaleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Patient\CartController;
use App\Http\Controllers\Patient\CatalogController;
use App\Http\Controllers\Patient\CheckoutController;
use App\Http\Controllers\Patient\OrderController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/catalog');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/autocomplete/search', [CatalogController::class, 'autocomplete'])->name('catalog.autocomplete');
Route::get('/catalog/{medicine}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::middleware('role:pasien')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{medicine}', [CartController::class, 'add'])->name('cart.add');
        Route::put('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    });

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
        Route::resource('medicines', MedicineController::class)->except('show');
        Route::resource('medicine-batches', BatchController::class)->except('show');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::post('/reports/generate-job', [ReportController::class, 'generateJob'])->name('reports.generate-job');
        Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
        Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/latest', [MonitoringController::class, 'latest'])->name('monitoring.latest');
        Route::get('/orders/latest-count', fn () => response()->json(['count' => Order::whereDate('created_at', now())->count()]))->name('orders.latest-count');
        Route::get('/error-logs', [ErrorLogController::class, 'index'])->name('error-logs.index');
        Route::post('/error-logs/{errorLog}/resolve', [ErrorLogController::class, 'resolve'])->name('error-logs.resolve');
        Route::post('/error-logs/simulate', [ErrorLogController::class, 'simulate'])->name('error-logs.simulate');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/simulations', [SimulationController::class, 'index'])->name('simulations.index');
        Route::post('/simulations/low-stock', [SimulationController::class, 'lowStock'])->name('simulations.low-stock');
        Route::post('/simulations/expired', [SimulationController::class, 'expired'])->name('simulations.expired');
        Route::post('/simulations/error', [SimulationController::class, 'error'])->name('simulations.error');
    });

    Route::prefix('apoteker')->name('apoteker.')->middleware('role:apoteker')->group(function () {
        Route::get('/dashboard', [ApotekerController::class, 'dashboard'])->name('dashboard');
        Route::get('/prescriptions', [ApotekerController::class, 'prescriptions'])->name('prescriptions');
        Route::get('/prescriptions/{order}', [ApotekerController::class, 'showPrescription'])->name('prescriptions.show');
        Route::post('/prescriptions/{order}/approve', [ApotekerController::class, 'approve'])->name('prescriptions.approve');
        Route::post('/prescriptions/{order}/reject', [ApotekerController::class, 'reject'])->name('prescriptions.reject');
        Route::get('/stock-alerts', [ApotekerController::class, 'stockAlerts'])->name('stock-alerts');
    });

    Route::prefix('kasir')->name('kasir.')->middleware('role:kasir')->group(function () {
        Route::get('/dashboard', [SaleController::class, 'dashboard'])->name('dashboard');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    });
});
