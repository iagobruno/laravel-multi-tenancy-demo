<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $name = 'Loja de teste';
        $slug = str($name)->slug();

        DB::statement("DROP DATABASE IF EXISTS \"tenant-$slug\"");

        $tenant = \App\Models\Store::create([
            'id' => $slug,
            'settings' => [
                'site_name' => $name,
            ],
        ]);
        $tenant->domains()->create([
            'domain' => "$slug.localhost"
        ]);
    }
}
