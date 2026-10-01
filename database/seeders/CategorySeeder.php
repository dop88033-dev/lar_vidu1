<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tắt khóa ngoại và xóa sạch các bản ghi cũ trong bảng categories
        Schema::disableForeignKeyConstraints();
        DB::table('categories')->delete();
        Schema::enableForeignKeyConstraints();

        // 2. Danh sách 10 sản phẩm bóng bàn mẫu
        $products = [
            ['name' => 'Butterfly Harimoto Innerforce ALC CS', 'category_name' => 'Cốt vợt', 'price' => 3000000, 'quantity' => 10],
            ['name' => 'Butterfly Addoy 1000', 'category_name' => 'Vợt bóng bàn', 'price' => 550000, 'quantity' => 20],
            ['name' => 'Máy bắn bóng bàn Khổng Tử 01', 'category_name' => 'Phụ kiện', 'price' => 15000000, 'quantity' => 5],
            ['name' => 'BÓNG NITTAKU 3 STAR 40+ PREMIUM CLEAN', 'category_name' => 'Bóng bàn', 'price' => 230000, 'quantity' => 50],
            ['name' => 'Cây nhặt bóng đa năng', 'category_name' => 'Phụ kiện', 'price' => 390000, 'quantity' => 15],
            ['name' => 'Băng Cổ Tay NL Wristband 2', 'category_name' => 'Phụ kiện', 'price' => 180000, 'quantity' => 30],
            ['name' => 'Cốt vợt Stiga Clipper CR', 'category_name' => 'Cốt vợt', 'price' => 1450000, 'quantity' => 12],
            ['name' => 'Mặt vợt Butterfly Tenergy 05', 'category_name' => 'Mặt vợt', 'price' => 1500000, 'quantity' => 25],
            ['name' => 'Hộp bóng bàn DHS 3 sao D40+', 'category_name' => 'Bóng bàn', 'price' => 80000, 'quantity' => 40],
            ['name' => 'Keo tăng lực dán vợt Haifu Seamoon', 'category_name' => 'Phụ kiện', 'price' => 320000, 'quantity' => 18],
        ];

        // 3. Dùng forceFill để vượt qua rào cản $fillable
        foreach ($products as $item) {
            $cat = new Category();
            $cat->forceFill(array_merge($item, [
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]))->save();
        }
    }
}