<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract1s', function (Blueprint $table) {
            $table->id();

            // علاقات
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            // بيانات العقد
            $table->string('landlord_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date')->nullable();
            $table->decimal('rent_amount', 10, 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('terms_and_conditions_extra')->nullable();

            // توقيعات
            $table->string('tenant_signature_path')->nullable();
            $table->string('landlord_signature_path')->nullable();
            //$table->string('witness1_signature_path')->nullable();
            //$table->string('witness2_signature_path')->nullable();

            // بيانات التوظيف
            $table->date('hired_date')->nullable();
            $table->string('hired_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract1s');
    }
};
