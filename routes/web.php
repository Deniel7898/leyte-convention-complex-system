<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\InventoriesController;
use App\Http\Controllers\ItemDistributionsController;
use App\Http\Controllers\Service_RecordsController;
use App\Http\Controllers\Purchase_RequestsController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['verify' => true]); // includes all verification routes

// Verification page
Route::get('/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.hold');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('auth')
    ->name('verification.resend');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::get('/verification/success', function () {
    return view('auth/verification-success');
})->name('verification.success');

// Check verification status API
Route::middleware('auth')->get('/check-verification-status', function (Illuminate\Http\Request $request) {
    return response()->json([
        'verified' => $request->user()->hasVerifiedEmail()
    ]);
});

/*--------------------------------------------------------------------------
// Live Search Routes (ALL PROTECTED)
--------------------------------------------------------------------------*/
Route::get('/items/live-search', [ItemsController::class, 'liveSearch'])->name('items.liveSearch');
Route::get('/inventory/live-search', [InventoriesController::class, 'liveSearch'])->name('inventory.liveSearch');
Route::get('/item_distributions/live-search', [ItemDistributionsController::class, 'liveSearch'])->name('item_distributions.liveSearch');
Route::get('/service_records/live-search', [Service_RecordsController::class, 'liveSearch'])->name('service_records.liveSearch');

/*--------------------------------------------------------------------------
// Inventory (Route for restocking)
--------------------------------------------------------------------------*/
Route::get('/inventory/show-stock', [InventoriesController::class, 'show_stock'])->name('inventory.show_stock');
Route::post('/inventory/add-stock', [InventoriesController::class, 'add_stock'])->name('inventory.add_stock');

// -------------------------
// Protected Routes (Admin & Verified Staff)
// -------------------------
Route::middleware(['auth', 'admin'])->group(function () {

    // Home
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home/qr/{code}', [App\Http\Controllers\HomeController::class, 'getItemByQrCode']);

    // Users (Admin only)
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Inventory
    Route::resource('items', ItemsController::class);
    Route::resource('inventory', InventoriesController::class);
    Route::get('/inventory/{item}/history', [InventoriesController::class, 'view_history'])->name('inventory.history');

    // Item Distributions
    Route::resource('item_distributions', ItemDistributionsController::class);
    Route::get('/item-distributions/{id}', [ItemDistributionsController::class, 'showReturnForm'])->name('item_distributions.return_form');
    Route::post('/item-distributions/{id}/return', [ItemDistributionsController::class, 'returnItem'])->name('item_distributions.returnItem');

    // Service Records
    Route::resource('service_records', Service_RecordsController::class);
    Route::get('/service/show-service/{id}', [Service_RecordsController::class, 'show_service'])->name('service_records.show_service');
    Route::post('/service/{id}/complete-service', [Service_RecordsController::class, 'complete_service'])->name('service_records.complete_service');

    // References
    Route::resource('categories', App\Http\Controllers\CategoriesController::class);
    Route::resource('units', App\Http\Controllers\UnitsController::class);

    // Purchase Requests
    Route::resource('purchase-requests', Purchase_RequestsController::class);
    Route::get('/purchase-requests/{id}/print', [Purchase_RequestsController::class, 'print'])->name('purchase-requests.print');
    Route::get('/purchase-requests/search-items', [Purchase_RequestsController::class, 'searchItems'])->name('purchase-requests.searchItems');
});
