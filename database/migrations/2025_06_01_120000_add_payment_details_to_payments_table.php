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
            // معلومات الدافع والمستلم
            $table->string('payer_name')->after('payment_method'); // اسم الدافع
            $table->string('receiver_name')->after('payer_name'); // اسم المستلم
            
            // معلومات التحويل (للتحويل البنكي والمحافظ الإلكترونية)
            $table->string('bank_name')->nullable()->after('receiver_name'); // اسم البنك
            $table->string('transfer_reference')->nullable()->after('bank_name'); // الرقم المرجعي للحوالة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payer_name',
                'receiver_name', 
                'bank_name',
                'transfer_reference'
            ]);
        });
    }
};
