<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kondisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pemilik data
            $table->string('nama_kondisi', 120);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Unik per user
            $table->unique(['user_id', 'nama_kondisi'], 'kondisis_user_nama_unique');
        });
               
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kondisis');
    }
};
