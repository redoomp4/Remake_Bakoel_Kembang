<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nama_pemasok', 150);
            $table->string('email', 191)->nullable();   // ← TIDAK pakai unique() di kolom
            $table->string('jenis', 50)->nullable();
            $table->string('nama_pic', 120)->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_telepon', 50)->nullable();
            $table->date('bergabung_sejak')->nullable();
            $table->timestamps();

            // Unik per user:
            $table->unique(['user_id', 'nama_pemasok'], 'pemasoks_user_nama_unique');
            $table->unique(['user_id', 'email'], 'pemasoks_user_email_unique'); // opsional tapi disarankan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasoks');
    }
};
