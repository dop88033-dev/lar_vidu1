<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'Cốt vợt Butterfly Viscaria FL',
            'Cốt vợt Stiga Clipper CR',
            'Cốt vợt DHS Fang Bo Carbon B2X',
            'Mặt vợt Butterfly Tenergy 05',
            'Mặt vợt Butterfly Dignics 09C',
            'Mặt vợt DHS Hurricane 3 Neo Tỉnh',
            'Hộp 3 quả bóng bàn DHS 3 sao D40+',
            'Hộp 6 quả bóng Nittaku Premium 40+',
            'Keo tăng lực dán vợt Haifu Seamoon',
            'Bao vợt bóng bàn Mizuno đệm dày',
        ];

        foreach ($products as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}