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
            if (!Schema::hasColumn('contract1s', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('hired_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract1s', function (Blueprint $table) {
            if (Schema::hasColumn('contract1s', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
        });
    }
};
