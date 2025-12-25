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
        Schema::create('debtor_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_invoice_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('product_name');
            $table->integer('pieces');
            $table->decimal('weight', 8, 2);
            $table->decimal('rate', 10, 2);
            $table->decimal('total', 12, 2);
            $table->boolean('status')->default(1); // 1 = active
            $table->foreignId('updated_by')->default(1)->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debtor_invoice_items');
    }
};
