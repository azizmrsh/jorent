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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('midname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['active', 'unactive'])->default('unactive');
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->string('document_photo')->nullable();
            $table->string('nationality')->nullable();
            $table->date('hired_date')->nullable();
            $table->string('hired_by')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
