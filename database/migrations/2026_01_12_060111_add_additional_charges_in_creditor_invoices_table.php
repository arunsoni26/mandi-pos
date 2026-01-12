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
        Schema::table('creditor_invoices', callback: function (Blueprint $table) {
            $table->decimal('additional_charges', 12, 2)->nullable()->default(0)->after('total_wage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creditor_invoices', function (Blueprint $table) {
            $table->dropColumn(['additional_charges']);
        });
    }
};
