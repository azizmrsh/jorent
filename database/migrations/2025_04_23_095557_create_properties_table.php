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
            $table->json('images')->nullable(); // Add this line for images
            
            // Foreign key to addresses table one to one
            $table->unsignedBigInteger('address_id')->nullable(); // Add this line for address_id
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null'); // Add this line for address_id foreign key

            
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
