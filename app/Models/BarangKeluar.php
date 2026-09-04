<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BarangKeluar extends Model
{
    use HasFactory;


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

    protected $casts = [
        'tanggal_keluar' => 'datetime',
        'harga_jual' => 'decimal:2',
        'total_harga_jual' => 'decimal:2',
    ];

    // Accessor Kode Barang untuk fallback kompatibilitas view
    public function getKodeBarangAttribute()
    {
        return $this->item?->kode_barang ?? '-';
    }

    /**
     * Relasi ke model Item berdasarkan item_id
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }


    /**
     * Relasi ke model Lokasi
     */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
   
    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi');
    }
    

    

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
