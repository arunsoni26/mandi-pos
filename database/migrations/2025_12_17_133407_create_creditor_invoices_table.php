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
        Schema::create('creditor_invoices', function (Blueprint $table) {
            $table->id();
            // Active / Raw Creditor
            $table->foreignId('creditor_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->date('invoice_date')->unique();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('total_wage', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->boolean('status')->default(1); // 1 = active
            $table->foreignId('updated_by')->default(1)->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(columns: ['creditor_id', 'invoice_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditor_invoices');
    }
};
