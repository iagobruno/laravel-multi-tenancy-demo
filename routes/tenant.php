<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| Feel free to customize them however you want. Good luck!
|
*/

Route::view('/', 'tenant')->name('home');

Route::post('/force-login', function () {
    $userId = request()->input('user_id');
    // $user = User::withoutGlobalScope('tenant')->findOrFail($userId);
    Auth::loginUsingId($userId);
    return redirect()->back();
})->name('login');

Route::post('/create-user', function () {
    User::factory()->create();
    return redirect()->back();
})->name('create_user');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->back();
})->name('logout');
