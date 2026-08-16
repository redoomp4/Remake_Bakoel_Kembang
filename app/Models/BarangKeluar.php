<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluars';

    protected $fillable = [
        'user_id',
        'item_id',
        'id_lokasi',
        'id_kondisi',
        'jumlah_keluar',
        'harga_jual',
        'total_harga_jual',
        'tanggal_keluar',
        'penerima',
        'lokasi_tujuan',
        'catatan',
        'jenis_transaksi',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi ke ITEM
    |--------------------------------------------------------------------------
    |
    | barang_keluars.item_id
    |          ↓
    |       items.id
    |
    | Item adalah master data barang.
    | BarangKeluar hanya mencatat transaksi keluarnya.
    |
    */
    public function item()
    {
        return $this->belongsTo(
            Item::class,
            'item_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi ke LOKASI
    |--------------------------------------------------------------------------
    */
    public function lokasi()
    {
        return $this->belongsTo(
            Lokasi::class,
            'id_lokasi',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi ke KONDISI
    |--------------------------------------------------------------------------
    */
    public function kondisi()
    {
        return $this->belongsTo(
            Kondisi::class,
            'id_kondisi',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi ke USER
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }
}