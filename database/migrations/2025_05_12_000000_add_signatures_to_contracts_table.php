<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->text('tenant_signature')->nullable();
            $table->text('witness_signature')->nullable();
            $table->text('landlord_signature')->nullable();
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('tenant_signature');
            $table->dropColumn('witness_signature');
            $table->dropColumn('landlord_signature');
        });
    }
};
