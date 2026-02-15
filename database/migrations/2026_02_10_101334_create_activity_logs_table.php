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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // who did it
            $table->string('action'); // created, updated, deleted
            $table->string('model_type'); // e.g., App\Models\CreditorInvoice
            $table->unsignedBigInteger('model_id'); // ID of the model
            $table->json('old_values')->nullable(); // old data for updates
            $table->json('new_values')->nullable(); // new data for updates
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
