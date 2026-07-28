<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('username', 100)->unique();
            $table->string('email', 191)->unique();       // 191 aman untuk utf8mb4
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            // role: superadmin, gudang, viewer (pakai string + default)
            $table->string('role', 20)->default('viewer');
            $table->string('status', 20)->default('Active');

            $table->string('position', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('photo', 255)->nullable();
            $table->text('note')->nullable();

            $table->boolean('is_active')->default(true);

            // ❗ perbaikan utama: nullable karena diisi saat login
            $table->timestamp('last_login')->nullable();

            $table->rememberToken();
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
