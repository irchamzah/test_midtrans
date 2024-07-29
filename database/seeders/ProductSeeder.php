<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create(['image' => 'products/product1.jpg', 'name' => 'Produk 1', 'description' => 'Deskripsi untuk Produk 1', 'price' => 100000]);
        Product::create(['image' => 'products/product2.jpg', 'name' => 'Produk 2', 'description' => 'Deskripsi untuk Produk 2', 'price' => 200000]);
        Product::create(['image' => 'products/product3.jpg', 'name' => 'Produk 3', 'description' => 'Deskripsi untuk Produk 3', 'price' => 300000]);
    }
}
