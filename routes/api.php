<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Appliance\Controllers\ApplianceController;
use App\Domain\Storage\Controllers\StorageController;
use App\Domain\Inventory\Controllers\InventoryController;
use App\Domain\Customer\Controllers\CustomerController;
use App\Domain\Sale\Controllers\SaleController;
use App\Domain\Credit\Controllers\CreditController;
use App\Domain\Payment\Controllers\PaymentController;
use App\Domain\PaymentMonitoring\Controllers\PaymentMonitoringController;
use App\Domain\Notification\Controllers\NotificationController;
use App\Domain\Dashboard\Controllers\DashboardController;



Route::apiResource('appliances',ApplianceController::class);

Route::apiResource('storages', StorageController::class);

Route::prefix('inventory')->group(function () {
    Route::get(
        '/',
        [InventoryController::class, 'index']
    );
    Route::post(
        '/stock-in',
        [InventoryController::class, 'stockIn']
    );
    Route::get(
        '/appliance/{appliance}',
        [InventoryController::class, 'byAppliance']
    );
    Route::get(
        '/storage/{storage}',
        [InventoryController::class, 'byStorage']
    );
    Route::patch(
        '/{inventory}/adjust',
        [InventoryController::class, 'adjust']
    );
    Route::get(
        '/{inventory}',
        [InventoryController::class, 'show']
    );
});


Route::apiResource(
    'customers',
    CustomerController::class
);


Route::apiResource(
    'sales',
    SaleController::class
)->only([
    'index',
    'show',
    'store',
]);



Route::prefix('credits')->group(function () {
    Route::get(
        '/',
        [CreditController::class, 'index']
    );
    Route::post(
        '/',
        [CreditController::class, 'store']
    );
    Route::get(
        '/upcoming',
        [CreditController::class, 'upcoming']
    );
    Route::get(
        '/overdue',
        [CreditController::class, 'overdue']
    );
    Route::get(
        '/completed',
        [CreditController::class, 'completed']
    );
    Route::get(
        '/{credit}/summary',
        [CreditController::class, 'summary']
    );
    Route::get(
        '/{credit}',
        [CreditController::class, 'show']
    );
    Route::patch(
        '/{credit}',
        [CreditController::class, 'update']
    );
});

Route::get(
    'payments',
    [PaymentController::class, 'index']
);
Route::get(
    'payments/{payment}',
    [PaymentController::class, 'show']
);
Route::get(
    'credits/{credit}/payments',
    [PaymentController::class, 'byCredit']
);
Route::post(
    'credits/{credit}/payments',
    [PaymentController::class, 'store']
);



Route::prefix(
    'payment-monitoring'
)->group(function () {
    Route::get(
        '/settings',
        [
            PaymentMonitoringController::class,
            'settings'
        ]
    );
    Route::put(
        '/settings',
        [
            PaymentMonitoringController::class,
            'updateSettings'
        ]
    );
    Route::post(
        '/run-now',
        [
            PaymentMonitoringController::class,
            'runNow'
        ]
    );
    Route::get(
        '/status',
        [
            PaymentMonitoringController::class,
            'status'
        ]
    );
});



Route::prefix('notifications')->group(function () {
    Route::get(
        '/',
        [NotificationController::class, 'index']
    );
    Route::get(
        '/unread',
        [NotificationController::class, 'unread']
    );
    Route::get(
        '/unread-count',
        [
            NotificationController::class,
            'unreadCount'
        ]
    );
    Route::patch(
        '/read-all',
        [
            NotificationController::class,
            'markAllAsRead'
        ]
    );
    Route::patch(
        '/{notification}/read',
        [
            NotificationController::class,
            'markAsRead'
        ]
    );
    Route::get(
        '/{notification}',
        [NotificationController::class, 'show']
    );
    Route::delete(
        '/{notification}',
        [
            NotificationController::class,
            'destroy'
        ]
    );
});


Route::prefix('dashboard')->group(function () {
    Route::get(
        '/',
        [
            DashboardController::class,
            'summary'
        ]
    );
    Route::get(
        '/inventory',
        [
            DashboardController::class,
            'inventory'
        ]
    );
    Route::get(
        '/sales',
        [
            DashboardController::class,
            'sales'
        ]
    );
    Route::get(
        '/credits',
        [
            DashboardController::class,
            'credits'
        ]
    );
});