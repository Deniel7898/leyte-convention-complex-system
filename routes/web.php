<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\InventoriesController;
use App\Http\Controllers\ItemDistributionsController;
use App\Http\Controllers\Service_RecordsController;
use App\Http\Controllers\ViewItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Purchase_RequestsController;
use App\Http\Controllers\QR_CodeController;

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
    return view('/auth.login');
});

Auth::routes();

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


/*--------------------------------------------------------------------------
// Live Search Routes (ALL PROTECTED)
--------------------------------------------------------------------------*/
Route::get('/items/live-search', [ItemsController::class, 'liveSearch'])->name('items.liveSearch');
Route::get('/inventory/live-search', [InventoriesController::class, 'liveSearch'])->name('inventory.liveSearch');
Route::get('/qr_codes/live-search', [QR_CodeController::class, 'liveSearch'])->name('qr_codes.liveSearch');
Route::get('/item_distributions/live-search', [ItemDistributionsController::class, 'liveSearch'])->name('item_distributions.liveSearch');

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
Route::resource('qr_codes', App\Http\Controllers\QR_CodeController::class)->middleware('auth'); // includes all CRUD routes for QR codes

/*--------------------------------------------------------------------------
// Purchase Requests Routes 
--------------------------------------------------------------------------*/
Route::get('/purchase_request/print_approved', [Purchase_RequestsController::class, 'printApproved'])->name('purchase_request.printApproved');
Route::post('purchase_request/{id}/status/{status}', [App\Http\Controllers\Purchase_RequestsController::class, 'updateStatus'])->name('purchase_request.updateStatus');
Route::resource('purchase_request', App\Http\Controllers\Purchase_RequestsController::class)->middleware('auth');
