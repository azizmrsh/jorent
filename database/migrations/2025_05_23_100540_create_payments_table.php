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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // معرف العقد
            $table->foreignId('contract_id')->constrained('contract1s')->onDelete('cascade');
            
            // رقم الدفعة الفريد
            $table->string('payment_number')->unique()->comment('رقم الدفعة الفريد');
            
            // معلومات المبلغ والعملة
            $table->decimal('amount', 10, 2)->comment('المبلغ');
            $table->string('currency', 3)->default('JOD')->comment('العملة');
            
            // تاريخ ووقت الدفع
            $table->date('payment_date')->comment('تاريخ الدفع');
            
            // أسماء الأطراف
            $table->string('payer_name')->comment('اسم الدافع');
            $table->string('receiver_name')->comment('اسم المستلم');
            
            // معلومات طريقة الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer', 'wallet', 'cliq'])->default('cash')->comment('طريقة الدفع');
            $table->string('bank_name')->nullable()->comment('اسم البنك');
            $table->string('transaction_id')->nullable()->comment('رقم المعاملة البنكية');
            $table->string('reference_number')->nullable()->comment('الرقم المرجعي');
            
            // حالة الدفع
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed')->comment('حالة الدفع');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
