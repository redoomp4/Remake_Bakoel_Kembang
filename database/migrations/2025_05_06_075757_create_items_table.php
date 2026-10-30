<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {

            // ==========================================
            // IDENTITAS INTERNAL
            $table->id();

            // Pemilik katalog
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Kode barang milik masing-masing user
            // Contoh:
            // User 1 -> BRG-001 = Mawar
            // User 2 -> BRG-001 = Melati
            $table->string('kode_barang', 50);


            // IDENTITAS PUBLIK
            // Token unik untuk URL publik / QR Code.
            // Tidak menggunakan kode_barang sebagai identitas publik
            // karena kode barang boleh sama antar-user.
            $table->uuid('public_token')
                ->unique();


            // DATA ITEM
            $table->string('nama_barang', 150);

            $table->foreignId('id_kategori')
                ->constrained('kategoris')
                ->cascadeOnDelete();

            $table->foreignId('id_satuan')
                ->constrained('satuans')
                ->cascadeOnDelete();

            $table->unsignedInteger('stok_minimum')
                ->default(0);

            $table->decimal('harga_dasar', 15, 2)
                ->unsigned()
                ->default(0);

            $table->text('deskripsi')
                ->nullable();

            $table->string('foto')
                ->nullable();

            // QR CODE ITEM
            // Menyimpan lokasi file QR.
            // Contoh:
            // qrcodes/items/qr_item_xxxxx.svg
            $table->string('qr_code')
                ->nullable();


            $table->timestamps();

            // UNIQUE PER USER / MULTI-TENANT
            $table->unique(
                ['user_id', 'kode_barang'],
                'items_user_kode_unique'
            );

            $table->unique(
                ['user_id', 'nama_barang'],
                'items_user_nama_unique'
            );


            $table->index(
                ['user_id', 'id_kategori']
            );

            $table->index(
                ['user_id', 'id_satuan']
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
