<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::truncate();

        Product::create([
           "product_name" => "Starter",
           "slug" => Str::slug("Starter"),
           "target_amount" => 68900,
            "base_amount" => 50,
        ]);

        Product::create([
            "product_name" => "Standard",
            "slug" => Str::slug("Standard"),
            "target_amount" => 137800,
            "base_amount" => 100,
        ]);

        Product::create([
            "product_name" => "Ambitious",
            "slug" => Str::slug("Ambitious"),
            "target_amount" => 137800,
            "base_amount" => 200,
        ]);

        Product::create([
            "product_name" => "Power Saver",
            "slug" => Str::slug("Power Saver"),
            "target_amount" => 413400,
            "base_amount" => 300,
        ]);

        Product::create([
            "product_name" => "Wealth Builder",
            "slug" => Str::slug("Wealth Builder"),
            "target_amount" => 551200,
            "base_amount" => 400,
        ]);

        Product::create([
            "product_name" => "Elite",
            "slug" => Str::slug("Elite"),
            "target_amount" => 689000,
            "base_amount" => 500,
        ]);

        Product::create([
            "product_name" => "Ultimate",
            "slug" => Str::slug("Ultimate"),
            "target_amount" => 1378000,
            "base_amount" => 1000,
        ]);
    }
}
