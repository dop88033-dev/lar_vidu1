<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Cốt vợt Butterfly Viscaria FL', 'Cốt vợt bóng bàn', 3600000, 20],
            ['Cốt vợt Stiga Clipper CR', 'Cốt vợt bóng bàn', 1450000, 30],
            ['Cốt vợt DHS Fang Bo Carbon B2X', 'Cốt vợt bóng bàn', 1200000, 25],
            ['Mặt vợt Butterfly Tenergy 05', 'Mặt vợt bóng bàn', 1500000, 50],
            ['Mặt vợt Butterfly Dignics 09C', 'Mặt vợt bóng bàn', 1850000, 40],
            ['Mặt vợt DHS Hurricane 3 Neo Tỉnh', 'Mặt vợt bóng bàn', 650000, 60],
            ['Hộp 3 quả bóng bàn DHS 3 sao D40+', 'Quả bóng bàn & Phụ kiện', 80000, 100],
            ['Hộp 6 quả bóng Nittaku Premium 40+', 'Quả bóng bàn & Phụ kiện', 220000, 80],
            ['Keo tăng lực dán vợt Haifu Seamoon', 'Quả bóng bàn & Phụ kiện', 320000, 50],
            ['Bao vợt bóng bàn Mizuno đệm dày', 'Quả bóng bàn & Phụ kiện', 250000, 45],
        ];

        foreach ($products as [$name, $categoryName, $price, $quantity]) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            Product::firstOrCreate(
                ['name' => $name, 'category_id' => $category->id],
                ['price' => $price, 'quantity' => $quantity]
            );
        }
    }
}