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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('fullname')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->decimal('total_price', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('shipping_status')->default('not_shipped');
            $table->string('ghn_order_code')->nullable()->index();
            $table->integer('ghn_total_fee')->default(0);
            $table->integer('to_district_id')->nullable();
            $table->string('to_ward_code')->nullable();
            $table->string('note')->nullable();
            $table->string('payment_method')->default('cod');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
