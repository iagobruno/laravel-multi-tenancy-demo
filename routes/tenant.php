<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Models\{Post, Product};

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        echo 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id') . '<br><br>';

        echo 'Configurações do site:';
        dump(tenant()->settings);

        // dump(\App\Models\User::all()->toArray());
    })->name('home');

    Route::get('/blog/{post:slug}', function (Post $post) {
        return $post;
    })->name('post_page');

    Route::get('/produto/{product:slug}', function (Product $product) {
        return $product;
    })->name('product_page');
});
