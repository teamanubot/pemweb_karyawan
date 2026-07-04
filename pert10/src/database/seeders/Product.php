<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class Product extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product')->insert([
            [
                'name' => 'Product 1',
                'description' => 'Description for Product 1',
                'price' => 10.99,
                'created_at' => "2023-06-18 21:00:58",
                'updated_at' => "2023-06-18 21:00:58",
            ],
            [
                'name' => 'Product 2',
                'description' => 'Description for Product 2',
                'price' => 19.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 3',
                'description' => 'Description for Product 3',
                'price' => 5.49,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
