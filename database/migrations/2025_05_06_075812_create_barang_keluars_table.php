<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {

            // IDENTITAS TRANSAKSI
            $table->id();

            // PEMILIK TRANSAKSI
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
 
            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            // DATA TRANSAKSI BARANG KELUAR
            $table->dateTime('tanggal_keluar');

            $table->unsignedInteger('jumlah_keluar');

            $table->decimal('harga_jual', 15, 2)
                ->unsigned()
                ->nullable();

            $table->decimal('total_harga_jual', 18, 2)
                ->unsigned()
                ->nullable();

            $table->string('penerima');

            $table->string('lokasi_tujuan');

            // RELASI TRANSAKSI
            $table->foreignId('id_lokasi')
                ->constrained('lokasis')
                ->restrictOnDelete();

            $table->foreignId('id_kondisi')
                ->constrained('kondisis')
                ->restrictOnDelete();

            // CATATAN
            $table->text('catatan')
                ->nullable();

            $table->timestamps();

            // INDEX
            $table->index([
                'user_id',
                'tanggal_keluar'
            ]);

            $table->index([
                'user_id',
                'item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};
