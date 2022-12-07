<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/*
php artisan migrate:fresh --seed && \
php artisan tenants:migrate-fresh && \
php artisan tenants:seed
*/

class TenantSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $domain = tenant()->domains?->first()?->domain;
        $email = \App\Models\User::factory(3)->create()->first()->email;
        $password = 'password';
        dump("Fake user to login on http://{$domain}. Email: \"{$email}\", Password: \"{$password}\"");
    }
}
