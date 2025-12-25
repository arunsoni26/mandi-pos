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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('customer_type', ['Active Creditor', 'Raw Creditor', 'Debtor'])->default('Active Creditor');
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->boolean('status')->default(1); // 1 = active
            $table->boolean('hide_dashboard')->default(1); // 1 = unhide
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
        Schema::dropIfExists('customers');
    }
};
