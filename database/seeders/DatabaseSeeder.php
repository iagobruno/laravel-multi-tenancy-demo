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
        \App\Models\Store::create([
            // 'id' => 'loja-de-test'
        ])
            ->domains()->create([
                'domain' => 'test.localhost'
            ]);


        \App\Models\Store::all()->runForEach(function () {
            \App\Models\User::factory(3)->create();
        });
    }
}
