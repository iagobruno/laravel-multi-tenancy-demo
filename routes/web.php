<?php

use Illuminate\Support\Facades\Route;
use App\Models\{Store};
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('welcome');
});

Route::view('/create-store', 'create-store');
Route::post('/create-store', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'min:2'],
        'subdomain' => ['required', 'string', 'min:4', 'alpha_num'],
    ]);

    $domain = $request->input('subdomain') . '.localhost';
    $store = Store::create([
        'name' => $data['name']
    ]);
    $store->domains()->create([
        'domain' => strtolower($domain),
    ]);
    return redirect(tenant_route($domain, 'home'));
})->name('create-store');
