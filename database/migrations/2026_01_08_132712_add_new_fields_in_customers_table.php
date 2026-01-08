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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('customer_type');
            $table->string('pan')->nullable()->after('mobile');
            $table->longText('profile_pic')->nullable()->after('pan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'pan', 'profile_pic']);
        });
    }
};
