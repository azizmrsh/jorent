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
        Schema::create('accs', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('midname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_verified_at')->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('password');
            $table->string('status')->default('active')->nullable(); // active, inactive, banned
            $table->enum('document_type', ['ID', 'passport', 'driver_license', 'residency_permit', 'other'])->nullable(); // Updated to include more document types
            $table->string('document_number')->nullable(); // رقم الوثيقة
            $table->string('document_photo')->nullable(); // صورة الوثيقة الرسمية
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
        Schema::dropIfExists('accs');
    }
};
