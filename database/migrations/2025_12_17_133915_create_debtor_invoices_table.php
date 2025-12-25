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
        Schema::create('debtor_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            // Which creditor this debtor bought from
            $table->foreignId('creditor_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->date('invoice_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('total_wage', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->boolean('status')->default(1); // 1 = active
            $table->foreignId('updated_by')->default(1)->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'debtor_customer_id',
                'creditor_id',
                'invoice_date'
            ], 'debtor_creditor_day_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debtor_invoices');
    }
};
