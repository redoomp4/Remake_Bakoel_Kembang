<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            // ✅ Kode string sebagai PRIMARY KEY (bukan auto-increment)
            $table->string('kode_barang', 50)->primary();

            // ✅ Multi-tenant
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Data utama
            $table->string('nama_barang', 150);
            $table->foreignId('id_kategori')->constrained('kategoris')->cascadeOnDelete();
            $table->foreignId('id_satuan')->constrained('satuans')->cascadeOnDelete();
            $table->foreignId('id_pemasok')->constrained('pemasoks')->cascadeOnDelete();

            $table->unsignedInteger('stok_minimum')->default(0);
            $table->unsignedDecimal('harga_dasar', 15, 2)->default(0);

            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();

            $table->timestamps();

            // ✅ Optional: nama barang unik per user (hapus jika tidak ingin dipaksa unik)
            $table->unique(['user_id', 'nama_barang'], 'items_user_nama_unique');

            // Index bantu filter/report
            $table->index(['user_id', 'id_kategori']);
            $table->index(['user_id', 'id_satuan']);
            $table->index(['user_id', 'id_pemasok']);
            $table->index(['user_id', 'kode_barang'], 'items_user_kode_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
