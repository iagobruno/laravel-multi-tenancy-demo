<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $tenants = \App\Models\Tenant::factory(2)->create();

        \App\Models\Tenant::factory()->create([
            'name' => 'TESTER',
            'subdomain' => 'test',
        ]);

        \App\Models\Tenant::all()->each(function ($tenant) {
            \App\Models\User::factory(3)->create([
                'tenant_id' => $tenant->id,
            ]);
        });
    }
}
