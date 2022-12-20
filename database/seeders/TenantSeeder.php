<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\{Collection, Customer, Post, Product, User};
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
        $users = User::all();

        Customer::factory(20)->create();

        foreach (range(0, 6) as $i) {
            $possibleStatuses = ['draft', 'published', 'scheduled', 'trashed'];
            $status = $possibleStatuses[$i % count($possibleStatuses)];

            Post::factory()
                ->setStatus($status)
                ->for($users->random(), 'author')
                ->hasCategories(rand(0, 3))
                ->create();
        }

        Product::factory(6)
            ->withVariants(3)
            ->hasAttached(Collection::factory())
            ->create();
    }
}
