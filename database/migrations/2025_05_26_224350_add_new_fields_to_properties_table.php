<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('total_area'); // 💲 السعر
            $table->string('main_image')->nullable()->after('price'); // 🖼️ الصورة الرئيسية
            $table->boolean('is_for_sale')->default(false)->after('main_image'); // 🏷️ للبيع
            $table->boolean('is_for_rent')->default(false)->after('is_for_sale'); // 🏷️ للإيجار
        });
    }

    /**
     * Reverse the migrations.
     */    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['price', 'main_image', 'is_for_sale', 'is_for_rent']);
        });
    }
};
