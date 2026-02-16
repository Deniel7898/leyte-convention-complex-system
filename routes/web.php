<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\InventoriesController;
use Illuminate\Support\Facades\Route;

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
Route::get('/inentory/live-search', [InventoriesController::class, 'liveSearch'])->name('inventory.liveSearch');

//inventory routes
Route::resource('items', App\Http\Controllers\ItemsController::class)->middleware('auth'); // includes all CRUD routes for items
Route::resource('inventory', App\Http\Controllers\InventoriesController::class)->middleware('auth'); // includes all CRUD routes for items

Route::resource('categories', App\Http\Controllers\CategoriesController::class)->middleware('auth'); // includes all CRUD routes for categories
Route::resource('units', App\Http\Controllers\UnitsController::class)->middleware('auth'); // includes all CRUD routes for units
Route::resource('qr_codes', controller: App\Http\Controllers\QR_CodeController::class)->middleware('auth'); // includes all CRUD routes for QR codes

Route::post('qr_codes/{qr_code}/mark-used',
    [App\Http\Controllers\QR_CodeController::class, 'markUsed']
)->name('qr_codes.markUsed')->middleware('auth');
