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
        Schema::table('contract1s', function (Blueprint $table) {
            $table->string('witness1_signature_path')->nullable();
            $table->string('witness2_signature_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract1s', function (Blueprint $table) {
            $table->dropColumn(['witness1_signature_path', 'witness2_signature_path']);
        });
    }
};
