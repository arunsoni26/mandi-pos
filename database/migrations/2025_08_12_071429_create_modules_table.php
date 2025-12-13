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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 2. Seed roles
        DB::table('modules')->insert([
            ['id' => 1, 'name' => 'Profile', 'slug' => 'profile', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Permissions', 'slug' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Users', 'slug' => 'users', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Customers', 'slug' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Employees', 'slug' => 'employees', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'POS', 'slug' => 'pos', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Invoices', 'slug' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Reports', 'slug' => 'reports', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
