<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
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
