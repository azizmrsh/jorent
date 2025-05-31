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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->enum('type1', ['building', 'villa', 'house', 'warehouse']); 
            $table->enum('type2', ['Commercial', 'Residential', 'Industrial']); 
            // Foreign key to accounts table one to many 
            $table->unsignedBigInteger('acc_id')->nullable();
            $table->foreign('acc_id')->references('id')->on('accs')->onDelete('set null');
            
            $table->date('birth_date')->nullable(); 
            $table->integer('floors_count')->nullable(); 
            $table->decimal('floor_area', 10, 2)->nullable(); 
            $table->decimal('total_area', 10, 2)->nullable(); 
            $table->json('features')->nullable();
            $table->string('image_path')->nullable(); // مسار الصورة الرئيسية
            
            // سنضيف foreign key للـ addresses في migration منفصل لاحقاً
            $table->unsignedBigInteger('address_id')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
