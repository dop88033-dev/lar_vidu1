<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Thêm 3 cột: giá, số lượng, ghi chú
            $table->integer('price')->default(0);
            $table->integer('quantity')->default(0);
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['price', 'quantity', 'notes']);
        });
    }
};
