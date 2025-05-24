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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('landlord_name');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date')->nullable(); // Optional, if applicable
            $table->decimal('rent_amount', 10, 2);
            //$table->string('payment_frequency'); // e.g., monthly, quarterly
            $table->enum('status', ['active', 'inactive'])->default('active');
            //$table->text('notes')->nullable();
            $table->string('terms_and_conditions_extra')->nullable(); // Path to the uploaded document
            $table->json('tenant_signature')->nullable(); // JSON object with signature data
            $table->json('landlord_signature')->nullable(); // JSON object with signature data
            $table->json('witness1_signature')->nullable(); // JSON object with signature data
            $table->json('witness2_signature')->nullable(); // JSON object with signature data
            $table->date('hired_date')->nullable(); // Date when the contract was signed
            $table->string('hired_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};