<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tắt khóa ngoại và xóa sạch các bản ghi cũ
        Schema::disableForeignKeyConstraints();
        DB::table('categories')->delete();
        DB::table('product_categories')->delete();
        Schema::enableForeignKeyConstraints();

        // 2. Nạp danh mục sản phẩm (ProductCategory)
        $productCategories = [
            ['name' => 'Cốt vợt', 'slug' => 'cot-vot', 'description' => 'Các loại cốt vợt bóng bàn chất lượng cao', 'is_active' => true],
            ['name' => 'Vợt bóng bàn', 'slug' => 'vot-bong-ban', 'description' => 'Vợt bóng bàn dán sẵn dành cho mọi cấp độ', 'is_active' => true],
            ['name' => 'Mặt vợt', 'slug' => 'mat-vot', 'description' => 'Mặt vợt bóng bàn cao cấp chính hãng', 'is_active' => true],
            ['name' => 'Bóng bàn', 'slug' => 'bong-ban', 'description' => 'Bóng bàn thi đấu và tập luyện chuẩn ITTF', 'is_active' => true],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'description' => 'Phụ kiện bóng bàn đầy đủ, tiện lợi', 'is_active' => true],
        ];

        foreach ($productCategories as $cat) {
            ProductCategory::create($cat);
        }

        // 3. Danh sách 10 sản phẩm bóng bàn mẫu kèm hình ảnh và mô tả đầy đủ
        $products = [
            [
                'name' => 'Butterfly Harimoto Innerforce ALC CS',
                'category_name' => 'Cốt vợt',
                'price' => 3000000,
                'quantity' => 10,
                'main_image' => 'https://images.unsplash.com/photo-1534158914592-062992fbe900?w=600&auto=format&fit=crop',
                'notes' => 'Cốt vợt carbon cao cấp dành cho lối đánh tấn công toàn diện.',
            ],
            [
                'name' => 'Butterfly Addoy 1000',
                'category_name' => 'Vợt bóng bàn',
                'price' => 550000,
                'quantity' => 20,
                'main_image' => 'https://images.unsplash.com/photo-1611251126744-8cb3855a88c7?w=600&auto=format&fit=crop',
                'notes' => 'Vợt dán sẵn chất lượng chuẩn Butterfly dành cho người mới chơi.',
            ],
            [
                'name' => 'Máy bắn bóng bàn Khổng Tử 01',
                'category_name' => 'Phụ kiện',
                'price' => 15000000,
                'quantity' => 5,
                'main_image' => 'https://images.unsplash.com/photo-1544698310-74ea9d1c8258?w=600&auto=format&fit=crop',
                'notes' => 'Máy tập đánh bóng bàn tự động nhiều chế độ bắn.',
            ],
            [
                'name' => 'BÓNG NITTAKU 3 STAR 40+ PREMIUM CLEAN',
                'category_name' => 'Bóng bàn',
                'price' => 230000,
                'quantity' => 50,
                'main_image' => 'https://images.unsplash.com/photo-1609710228159-0fa9bd7c0827?w=600&auto=format&fit=crop',
                'notes' => 'Bóng thi đấu quốc tế 3 sao chuẩn ITTF sản xuất tại Nhật Bản.',
            ],
            [
                'name' => 'Cây nhặt bóng đa năng',
                'category_name' => 'Phụ kiện',
                'price' => 390000,
                'quantity' => 15,
                'main_image' => 'https://images.unsplash.com/photo-1511067007398-7e4b90aab4bc?w=600&auto=format&fit=crop',
                'notes' => 'Dụng cụ thu gom bóng bàn nhanh chóng, tiết kiệm sức lực.',
            ],
            [
                'name' => 'Băng Cổ Tay NL Wristband 2',
                'category_name' => 'Phụ kiện',
                'price' => 180000,
                'quantity' => 30,
                'main_image' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=600&auto=format&fit=crop',
                'notes' => 'Băng đeo cổ tay thấm hút mồ hôi hiệu quả khi thi đấu.',
            ],
            [
                'name' => 'Cốt vợt Stiga Clipper CR',
                'category_name' => 'Cốt vợt',
                'price' => 1450000,
                'quantity' => 12,
                'main_image' => 'https://images.unsplash.com/photo-1519766304817-4f37bda74a29?w=600&auto=format&fit=crop',
                'notes' => 'Cốt vợt gỗ 7 lớp huyền thoại cho lực nảy cực tốt.',
            ],
            [
                'name' => 'Mặt vợt Butterfly Tenergy 05',
                'category_name' => 'Mặt vợt',
                'price' => 1500000,
                'quantity' => 25,
                'main_image' => 'https://images.unsplash.com/photo-1626248801379-51a0748a5f96?w=600&auto=format&fit=crop',
                'notes' => 'Mặt vợt xoáy đỉnh cao được nhiều vận động viên tin dùng.',
            ],
            [
                'name' => 'Hộp bóng bàn DHS 3 sao D40+',
                'category_name' => 'Bóng bàn',
                'price' => 80000,
                'quantity' => 40,
                'main_image' => 'https://images.unsplash.com/photo-1587280501635-68a0e82cd5ff?w=600&auto=format&fit=crop',
                'notes' => 'Hộp 10 quả bóng bàn DHS D40+ chất lượng cao.',
            ],
            [
                'name' => 'Keo tăng lực dán vợt Haifu Seamoon',
                'category_name' => 'Phụ kiện',
                'price' => 320000,
                'quantity' => 18,
                'main_image' => 'https://images.unsplash.com/photo-1526676037777-05a232554f77?w=600&auto=format&fit=crop',
                'notes' => 'Dầu tăng lực bôi mặt vợt giúp tăng độ nảy và kiểm soát.',
            ],
        ];

        // 4. Dùng forceFill để vượt qua rào cản $fillable
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