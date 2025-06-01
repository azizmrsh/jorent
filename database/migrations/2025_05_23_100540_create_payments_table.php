<?php

declare(strict_types=1);

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contract1s')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'wallet', 'cliq'])->default('cash');
            
            // معلومات الدافع والمستلم
            $table->string('payer_name'); // اسم الدافع
            $table->string('receiver_name'); // اسم المستلم
            
            // معلومات التحويل (للتحويل البنكي والمحافظ الإلكترونية)
            $table->string('bank_name')->nullable(); // اسم البنك
            $table->string('transfer_reference')->nullable(); // الرقم المرجعي للحوالة
            
            //$table->string('reference_number')->nullable(); // رقم مرجعي عام اختياري
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
