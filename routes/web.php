<?php

use App\Http\Requests\StoreTenant;
use App\Models\Tenant;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome');

Route::post('/create-site', function (StoreTenant $request) {
    $data = $request->validated();
    $tenant = Tenant::create($data);

    return redirect()->route('tenant_app', ['tenant' => $tenant->subdomain]);
})->name('create_site');
