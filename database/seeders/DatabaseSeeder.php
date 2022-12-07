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
        DB::statement('DROP DATABASE IF EXISTS "tenant-loja-de-test"');

        $tenant = \App\Models\Store::create([
            'id' => 'loja-de-test'
        ]);
        $tenant->domains()->create([
            'domain' => 'test.localhost'
        ]);
    }
}
