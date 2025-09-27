<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ConfirmatorController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Master\WilayahController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['role:adm-01'])->group(function () {
    Route::get('/admin/dashboard', [WilayahController::class, 'dashboard'])->name('admin.dashboard');
    // master
    Route::get('/admin/master/wilayah', [WilayahController::class, 'masterWilayah'])->name('admin.master.wilayah');
   
});

Route::middleware(['role:cashier'])->group(function () {
    Route::get('/cashier', [CashierController::class, 'dashboard']);
    Route::get('/cashier/bookings', [CashierController::class, 'dailyBookings']);
    Route::post('/cashier/payment', [CashierController::class, 'finalizePayment']);
});

Route::middleware(['role:confirmator'])->group(function () {
    Route::get('/confirmator', [ConfirmatorController::class, 'pendingBookings']);
    Route::post('/confirmator/verify/{id}', [ConfirmatorController::class, 'verifyPayment']);
});

Route::middleware(['role:member'])->group(function () {
    Route::get('/member', [MemberController::class, 'dashboard']);
    Route::get('/member/book', [MemberController::class, 'showBookingForm']);
    Route::post('/member/book', [MemberController::class, 'storeBooking']);
    Route::get('/member/history', [MemberController::class, 'bookingHistory']);
});

Route::get('/api/bookings', function() {
    $bookings = DB::table('bookings')
        ->join('courts', 'bookings.court_id', '=', 'courts.id')
        ->select('bookings.date as start', 'courts.name as title', 'bookings.start_time', 'bookings.end_time')
        ->where('booking_status', 'confirmed')
        ->get();

    return response()->json($bookings);
});
