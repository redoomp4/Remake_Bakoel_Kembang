<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();

            // kolom untuk FK komposit ke items
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('kode_barang', 50); // harus sama dengan items.kode_barang (string(50))

            // data transaksi
            $table->date('tanggal_keluar');
            $table->unsignedInteger('jumlah_keluar');
            $table->decimal('harga_jual', 15, 2)->unsigned()->nullable();
            $table->decimal('total_harga_jual', 18, 2)->unsigned()->nullable();
            $table->string('penerima');
            $table->string('lokasi_tujuan');

            // relasi lain
            $table->foreignId('id_lokasi')->constrained('lokasis')->restrictOnDelete();
            $table->foreignId('id_kondisi')->constrained('kondisis')->restrictOnDelete();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tanggal_keluar']);

            // FK komposit → didefinisikan SETELAH semua kolom ada
            $table->foreign(['user_id', 'kode_barang'])
                  ->references(['user_id', 'kode_barang'])
                  ->on('items')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};
