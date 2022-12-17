<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Store, User};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => $pass = '12345678'
        ]);

        $name = 'Loja de teste';
        $slug = str($name)->slug();

        DB::statement("DROP DATABASE IF EXISTS \"tenant-$slug\"");

        $tenant = Store::create([
            'id' => $slug,
            'owner_id' => $admin->id,
            'settings' => [
                'site_name' => $name,
            ],
        ]);
        $tenant->createDomain($domain = "$slug.localhost");

        dump("Fake user to login on http://{$domain}. Email: \"{$admin->email}\", Password: \"{$pass}\"");
    }
}
