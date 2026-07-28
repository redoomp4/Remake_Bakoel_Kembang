<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();

            // kolom yang dibutuhkan FK komposit
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('kode_barang', 50); // harus sama dgn items.kode_barang

            // data transaksi
            $table->unsignedInteger('jumlah');
            $table->unsignedDecimal('harga_satuan', 15, 2);
            $table->unsignedDecimal('total_harga', 18, 2)->default(0);
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluarsa')->nullable();

            // relasi lain
            $table->foreignId('id_pemasok')->constrained('pemasoks')->restrictOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasis')->restrictOnDelete();
            $table->foreignId('id_kondisi')->constrained('kondisis')->restrictOnDelete();

            $table->text('catatan')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tanggal_masuk']);

            // FK komposit → didefinisikan SETELAH semua kolom ada
            $table->foreign(['user_id', 'kode_barang'])
                  ->references(['user_id', 'kode_barang'])
                  ->on('items')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
