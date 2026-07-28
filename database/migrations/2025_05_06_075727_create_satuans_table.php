<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satuans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('nama_satuan');
        $table->timestamps();

        // unik per user
        $table->unique(['user_id', 'nama_satuan'], 'satuans_user_nama_unique');
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuans');
    }
};
