<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GoogleSheetsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });

/** ホスト用 Route */
// $user->role = admin を持つ
Route::middleware(["auth",'role:admin'])->group(function(){
    Route::get('/', [WebController::class, "index"])->name("home.index");

    Route::prefix("users")->group(function () {
        Route::get('/', [UserController::class, "index"])->name("users.index");
        Route::get('{code}', [UserController::class, "show"])->name("users.show");
        Route::post('{code}', [UserController::class, "update"])->name("users.update");
    });
    Route::prefix("companies")->group(function () {
        Route::get('/', [CompanyController::class, "index"])->name("companies.index");
        Route::get('{company_code}', [CompanyController::class, "show"])->name("companies.show");
        Route::post('{company_code?}', [CompanyController::class, "store"])->name("companies.store");
    });
});
/** クライアント用 Route */
// Route::middleware(["auth",'role:user'])->group(function(){
//     Route::get('/', [WebController::class, "user"])->name("home.user");
//     // $user->role = admin または user_company に権限を持つ user
//     Route::middleware(["user_company_role"])->group(function(){

//     });

// });


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


