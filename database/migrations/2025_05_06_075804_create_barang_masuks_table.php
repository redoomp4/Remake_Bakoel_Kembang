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

            // Pemilik transaksi
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Relasi langsung ke ITEM
            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            // Data transaksi barang masuk
            $table->unsignedInteger('jumlah');

            $table->decimal('harga_satuan', 15, 2)
                ->unsigned();

            $table->decimal('total_harga', 18, 2)
                ->unsigned()
                ->default(0);

            $table->dateTime('tanggal_masuk');

            $table->date('tanggal_kadaluarsa')
                ->nullable();

            // Relasi transaksi
            $table->foreignId('id_pemasok')
                ->constrained('pemasoks')
                ->restrictOnDelete();

            $table->foreignId('id_lokasi')
                ->constrained('lokasis')
                ->restrictOnDelete();

            $table->foreignId('id_kondisi')
                ->constrained('kondisis')
                ->restrictOnDelete();

            $table->text('catatan')
                ->nullable();

            $table->timestamps();

            // Index
            $table->index(['user_id', 'tanggal_masuk']);
            $table->index(['user_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
