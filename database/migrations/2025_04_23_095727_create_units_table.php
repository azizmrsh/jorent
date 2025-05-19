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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')->constrained()->cascadeOnDelete(); // العقار الأب

            $table->string('name'); 
            $table->integer('unit_number')->nullable(); 
            
            
            $table->enum('unit_type', ['apartment','studio', 'office', 'shop', 'warehouse', 'villa','house','building'])->default('apartment'); //'penthouse', 'duplex', 'townhouse', 'loft'
            
            $table->decimal('area', 10, 2)->nullable(); 
            
            $table->json('unit_details')->nullable(); 
            // $table->string('unit_details')->nullable(); // تفاصيل الوحدة مثل عدد الغرف، الحمامات، المطبخ، الخزائن، الشرفة، الحديقة، المسبح، الساونا، الجيم، الخ.
            // $table->integer('floor_number')->nullable(); 

            $table->json('features')->nullable(); 

$table->json('images')->nullable(); // Column to store multiple image paths as JSON
            
            $table->enum('status', ['available', 'rented', 'under_maintenance', 'unavailable', 'reserved', 'not_confirmed'])->default('available'); 

            $table->decimal('rental_price', 10, 2)->nullable(); // الإيجار

            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
