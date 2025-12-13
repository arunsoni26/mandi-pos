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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->integer('priority');
            $table->enum('status', [0,1])->default('1');
            $table->timestamps();
        });

        // Seed roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Super Admin', 'slug' => 'superadmin', 'priority' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Admin', 'slug' => 'admin', 'priority' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Employee', 'slug' => 'employee', 'priority' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Customer', 'slug' => 'customer', 'priority' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->default(value: 2)->constrained(); // default to customer or user
        });
        
        // Seed superadmin
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'superadmin@dksahu.co.in',
                'email_verified_at' => now(),
                'password' => bcrypt(12345678),
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        Schema::dropIfExists('roles');
    }
};
