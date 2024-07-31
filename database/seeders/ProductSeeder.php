<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('carts')->delete();

        // Menghapus semua data di tabel products
        Product::query()->delete();

        Product::create(['image' => 'products/converse_hitam.jpg', 'name' => 'Converse Hitam', 'description' => 'Deskripsi untuk Converse Hitam', 'price' => 1000]);
        Product::create(['image' => 'products/converse_putih.jpg', 'name' => 'Converse Putih', 'description' => 'Deskripsi untuk Converse Putih', 'price' => 1200]);
        Product::create(['image' => 'products/converse_merah.jpg', 'name' => 'Converse Merah', 'description' => 'Deskripsi untuk Converse Merah', 'price' => 1300]);
    }
}
