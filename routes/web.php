<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\InventoriesController;
use App\Http\Controllers\ViewItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Purchase_RequestsController;


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
    return view('welcome');
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

// Live search route for AJAX
Route::get('/items/live-search', [ItemsController::class, 'liveSearch'])->name('items.liveSearch');
Route::get('/inventory/live-search', [InventoriesController::class, 'liveSearch'])->name('inventory.liveSearch');
Route::get('/viewItem/live-search', [ViewItemController::class, 'liveSearch'])->name('viewItem.liveSearch');

//inventory routes
Route::resource('items', App\Http\Controllers\ItemsController::class)->middleware('auth'); // includes all CRUD routes for items
Route::resource('inventory', App\Http\Controllers\InventoriesController::class)->middleware('auth'); // includes all CRUD routes for items

//view items routes
Route::resource('viewItem', App\Http\Controllers\ViewItemController::class)->middleware('auth'); // includes all CRUD routes for view items
Route::get('/viewItem/create/{item?}', [ViewItemController::class, 'create'])->name('viewItem.create'); //for the add modal show
Route::get('/viewItem/edit/{item?}', [ViewItemController::class, 'edit'])->name('viewItem.edit'); //for the add modal show
Route::delete('/viewItem/{inventory}', [ViewItemController::class, 'destroy'])->name('viewItem.destroy');

Route::resource('categories', App\Http\Controllers\CategoriesController::class)->middleware('auth'); // includes all CRUD routes for categories
Route::resource('units', App\Http\Controllers\UnitsController::class)->middleware('auth'); // includes all CRUD routes for units
Route::resource('qr_codes', controller: App\Http\Controllers\QR_CodeController::class)->middleware('auth'); // includes all CRUD routes for QR codes

Route::post('qr_codes/{qr_code}/mark-used',
    [App\Http\Controllers\QR_CodeController::class, 'markUsed']
)->name('qr_codes.markUsed')->middleware('auth');

Route::get('qr_codes/{id}/print',
    [App\Http\Controllers\QR_CodeController::class, 'printLabel']
)->name('qr_codes.print')->middleware('auth');


Route::get('/purchase_request/print_approved', 
    [Purchase_RequestsController::class, 'printApproved']
)->name('purchase_request.printApproved');

Route::post('purchase_request/{id}/status/{status}',
    [App\Http\Controllers\Purchase_RequestsController::class, 'updateStatus'])
    ->name('purchase_request.updateStatus');
                
Route::resource('purchase_request', App\Http\Controllers\Purchase_RequestsController::class)
    ->middleware('auth');
