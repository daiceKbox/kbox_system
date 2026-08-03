<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GoogleSheetsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [TestController::class, "index"])->name("test");

Route::prefix("companies")->group(function () {
    Route::get('/', [CompanyController::class, "index"])->name("companies.index");
    Route::get('{code}', [CompanyController::class, "show"])->name("companies.show");
    Route::post('{code}', [CompanyController::class, "update"])->name("companies.update");
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


