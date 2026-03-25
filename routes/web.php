<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\InventoriesController;
use App\Http\Controllers\ItemDistributionsController;
use App\Http\Controllers\Service_RecordsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Purchase_RequestsController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\HomeController;
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

Route::get('/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.hold');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('auth')
    ->name('verification.resend');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::middleware('auth')->get('/check-verification-status', function (Illuminate\Http\Request $request) {
    $user = $request->user();
    return response()->json([
        'verified' => $user->hasVerifiedEmail()
    ]);
});

// web.php
Route::get('/verification/success', function () {
    return view('auth/verification-success'); // Blade template
})->name('verification.success');

Route::get('/home', function () {
    return view('home');
})->middleware(['auth'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('users', App\Http\Controllers\UserController::class)->middleware('auth'); // includes all CRUD routes for users
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*--------------------------------------------------------------------------
// Home Routes
--------------------------------------------------------------------------*/
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home/qr/{code}', [App\Http\Controllers\HomeController::class, 'getItemByQrCode']);

/*--------------------------------------------------------------------------
// Live Search Routes (ALL PROTECTED)
--------------------------------------------------------------------------*/
Route::get('/items/live-search', [ItemsController::class, 'liveSearch'])->name('items.liveSearch');
Route::get('/inventory/live-search', [InventoriesController::class, 'liveSearch'])->name('inventory.liveSearch');
Route::get('/item_distributions/live-search', [ItemDistributionsController::class, 'liveSearch'])->name('item_distributions.liveSearch');
Route::get('/service_records/live-search', [Service_RecordsController::class, 'liveSearch'])->name('service_records.liveSearch');

/*--------------------------------------------------------------------------
// Inventory Routes 
--------------------------------------------------------------------------*/
Route::resource('items', App\Http\Controllers\ItemsController::class)->middleware('auth'); // includes all CRUD routes for items
Route::get('/inventory/show-stock', [InventoriesController::class, 'show_stock'])->name('inventory.show_stock');
Route::post('/inventory/add-stock', [InventoriesController::class, 'add_stock'])->name('inventory.add_stock');
Route::get('/inventory/{item}/history', [InventoriesController::class, 'view_history'])->name('inventory.history');
Route::resource('inventory', App\Http\Controllers\InventoriesController::class)->middleware('auth');

/*--------------------------------------------------------------------------
// Item Distributions Routes 
--------------------------------------------------------------------------*/
Route::resource('item_distributions', App\Http\Controllers\ItemDistributionsController::class)->middleware('auth'); // includes all CRUD routes for item distributions
Route::get('/item-distributions/{id}', [ItemDistributionsController::class, 'showReturnForm'])->name('item_distributions.return_form');
Route::post('/item-distributions/{id}/return', [ItemDistributionsController::class, 'returnItem'])->name('item_distributions.returnItem');
Route::post('/item-distributions/{id}/undo', [ItemDistributionsController::class, 'undoCompletion'])->name('item_distributions.undo');

/*--------------------------------------------------------------------------
// Item Service Records Routes 
--------------------------------------------------------------------------*/
Route::resource('service_records', App\Http\Controllers\Service_RecordsController::class)->middleware('auth'); // includes all CRUD routes for item service records
Route::get('/service/show-service/{id}', [Service_RecordsController::class, 'show_service'])->name('service_records.show_service');
Route::post('/service/{id}/complete-service', [Service_RecordsController::class, 'complete_service'])->name('service_records.complete_service');
Route::post('/service-records/{id}/undo', [Service_RecordsController::class, 'undoCompletion'])->name('service_records.undo');

/*--------------------------------------------------------------------------
// References Routes 
--------------------------------------------------------------------------*/
Route::resource('categories', App\Http\Controllers\CategoriesController::class)->middleware('auth'); // includes all CRUD routes for categories
Route::resource('units', App\Http\Controllers\UnitsController::class)->middleware('auth'); // includes all CRUD routes for units

/*--------------------------------------------------------------------------
// Purchase Requests Routes 
--------------------------------------------------------------------------*/
Route::resource('purchase-requests', App\Http\Controllers\Purchase_RequestsController::class)->middleware('auth'); // includes all CRUD routes for purchase requests
Route::get('/purchase-requests/{id}/print', [Purchase_RequestsController::class, 'print'])->name('purchase-requests.print')->middleware('auth');
Route::get('/purchase-requests/search-items', [Purchase_RequestsController::class, 'searchItems'])->name('purchase-requests.searchItems')->middleware('auth');