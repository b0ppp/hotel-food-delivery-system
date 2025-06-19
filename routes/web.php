<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- PASTIKAN BARIS INI ADA
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SopViolationController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Receptionist\OrderController as ReceptionistOrderController;
use App\Http\Controllers\Kitchen\DashboardController as KitchenDashboardController;
use App\Http\Controllers\Delivery\DashboardController as DeliveryDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- LOGIKA PENGALIHAN UTAMA ---
// Rute ini akan menjadi titik masuk aplikasi Anda
Route::get('/', function () {
    // 1. Cek apakah pengguna sudah login
    if (Auth::check()) {
        // Jika sudah, dapatkan peran dan alihkan ke dashboard yang sesuai
        $user = Auth::user();
        if ($user->role) {
            switch ($user->role->role_name) {
                case 'Admin':
                    return redirect()->route('admin.dashboard');
                case 'Resepsionis':
                    return redirect()->route('receptionist.order.create');
                case 'Staf Dapur':
                    return redirect()->route('kitchen.dashboard');
                case 'Staf Antar':
                    return redirect()->route('delivery.dashboard');
            }
        }
        // Jika pengguna ada tapi tidak punya peran, logout untuk keamanan
        Auth::logout();
        return redirect()->route('login')->with('error', 'Peran Anda tidak dapat ditemukan.');
    }

    // 2. Jika belum login, selalu arahkan ke halaman login
    return redirect()->route('login');
});


// --- RUTE PENYORTIR SETELAH LOGIN ---
// Rute ini penting sebagai tujuan default setelah pengguna mengisi form login.
Route::get('/dashboard', function () {
    // Logika ini sama persis dengan di atas, untuk menangani pengalihan pasca-login.
    $user = Auth::user();
    if ($user->role) {
        switch ($user->role->role_name) {
            case 'Admin': return redirect()->route('admin.dashboard');
            case 'Resepsionis': return redirect()->route('receptionist.order.create');
            case 'Staf Dapur': return redirect()->route('kitchen.dashboard');
            case 'Staf Antar': return redirect()->route('delivery.dashboard');
        }
    }
    Auth::logout();
    return redirect()->route('login')->with('error', 'Peran Anda tidak dapat ditemukan.');
})->middleware(['auth', 'verified'])->name('dashboard');


// --- SISA RUTE APLIKASI (Tidak ada perubahan) ---

// Rute Manajemen Profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Grup Rute Admin
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::resource('menu-items', MenuItemController::class)->names('menuitems');
        Route::resource('rooms', RoomController::class)->names('rooms');
        Route::resource('room-types', RoomTypeController::class)->names('roomtypes');
        Route::get('/sop-violations', [SopViolationController::class, 'index'])->name('sop-violations.index');
    });

// Grup Rute Resepsionis
Route::middleware(['auth', 'receptionist'])
    ->prefix('receptionist')
    ->name('receptionist.')
    ->group(function () {
        Route::get('/order/create', [ReceptionistOrderController::class, 'create'])->name('order.create');
        Route::post('/orders', [ReceptionistOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/history', [ReceptionistOrderController::class, 'history'])->name('orders.history');
        Route::delete('/orders/{order}', [ReceptionistOrderController::class, 'destroy'])->name('orders.destroy');
    });

// Grup Rute Staf Dapur
Route::middleware(['auth', 'kitchen'])
    ->prefix('kitchen')
    ->name('kitchen.')
    ->group(function () {
        Route::get('/dashboard', [KitchenDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/orders/{order}/mark-as-ready', [KitchenDashboardController::class, 'markAsReady'])->name('orders.mark-as-ready');
        Route::patch('/orders/{order}/uncheck', [KitchenDashboardController::class, 'uncheck'])->name('orders.uncheck');
    });

// Grup Rute Staf Antar
Route::middleware(['auth', 'delivery'])
    ->prefix('delivery')
    ->name('delivery.')
    ->group(function () {
        Route::get('/dashboard', [DeliveryDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/orders/{order}/assign-staff', [DeliveryDashboardController::class, 'assignStaff'])->name('orders.assign-staff');
        Route::patch('/orders/{order}/update-status', [DeliveryDashboardController::class, 'updateStatus'])->name('orders.update-status');
    });

Route::redirect('/profile', '/');

// Memuat rute-rute autentikasi (login, register khusus admin, logout, dll.)
require __DIR__.'/auth.php';