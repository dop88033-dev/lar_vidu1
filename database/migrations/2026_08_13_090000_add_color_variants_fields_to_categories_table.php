<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('category_name')->nullable()->after('name');
            $table->string('main_image')->nullable()->after('category_name');
            $table->boolean('is_active')->default(true)->after('notes');
            $table->longText('colors')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['category_name', 'main_image', 'is_active', 'colors']);
        });
    }
};
