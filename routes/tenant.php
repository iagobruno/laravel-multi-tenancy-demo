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

Route::view('/', 'tenant')->name('tenant_app');

Route::post('/force-login', function () {
    $userId = request()->input('user_id');
    $user = User::withoutGlobalScope('tenant')->findOrFail($userId);
    Auth::login($user);
    return redirect()->back();
})->name('login');

Route::post('/create-user', function () {
    User::factory()->create([
        'tenant_id' => tenant()->id
    ]);
    return redirect()->back();
})->name('create_user');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->back();
})->name('logout');
