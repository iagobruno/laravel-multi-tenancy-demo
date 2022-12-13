<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\{Post, User};
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
        $admin = User::factory()->admin()->create([
            'email' => 'admin@admin.com',
            'password' => $pass = '12345678'
        ]);
        $domain = tenant()->domains?->first()?->domain;

        dump("Fake user to login on http://{$domain}. Email: \"{$admin->email}\", Password: \"{$pass}\"");

        Post::factory(6)
            ->forAuthor()
            ->create();
    }
}
