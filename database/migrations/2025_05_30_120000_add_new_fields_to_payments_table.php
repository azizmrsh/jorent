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
        Schema::table('payments', function (Blueprint $table) {
            // إضافة الحقول الجديدة فقط إذا لم تكن موجودة
            if (!Schema::hasColumn('payments', 'payment_number')) {
                $table->string('payment_number')->unique()->nullable()->comment('رقم الدفعة الفريد');
            }
            
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->default('JOD')->comment('العملة');
            }
            
            if (!Schema::hasColumn('payments', 'payer_name')) {
                $table->string('payer_name')->nullable()->comment('اسم الدافع');
            }
            
            if (!Schema::hasColumn('payments', 'receiver_name')) {
                $table->string('receiver_name')->nullable()->comment('اسم المستلم');
            }
            
            if (!Schema::hasColumn('payments', 'bank_name')) {
                $table->string('bank_name')->nullable()->comment('اسم البنك');
            }
            
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->comment('رقم المعاملة البنكية');
            }
            
            if (!Schema::hasColumn('payments', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])
                      ->default('completed')
                      ->comment('حالة الدفع');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_number',
                'currency', 
                'payer_name',
                'receiver_name',
                'bank_name',
                'transaction_id',
                'payment_status'
            ]);
        });
    }
};
