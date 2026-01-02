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
        Schema::table('debtor_invoices', function (Blueprint $table) {
            $table->dropForeign(['creditor_id']);
            $table->unsignedBigInteger('creditor_id')->change();
            $table->foreign('creditor_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();
            $table->dropForeign(['debtor_customer_id']);
            $table->unsignedBigInteger('debtor_customer_id')->change();
            $table->foreign('debtor_customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
