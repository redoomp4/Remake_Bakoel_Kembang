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
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id(); // PK
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pemilik data
            $table->string('kategori', 100); // batasi panjang seperlunya
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Unik per user
            $table->unique(['user_id', 'kategori'], 'kategoris_user_kategori_unique');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kategoris');
    }
};
