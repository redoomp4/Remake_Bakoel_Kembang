<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BarangMasuk extends Model
{
    use HasFactory;
    protected $table = 'barang_masuks';
    protected $fillable = [
        'user_id',
        'item_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'id_pemasok',
        'id_lokasi',
        'id_kondisi',
        'catatan',
    ];
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'tanggal_kadaluarsa' => 'date',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Pemasok
     */
    public function pemasok()
    {
        return $this->belongsTo(
            Pemasok::class,
            'id_pemasok',
            'id'
        );
    }
    /**
     * Lokasi penyimpanan
     */
    public function lokasi()
    {
        return $this->belongsTo(
            Lokasi::class,
            'id_lokasi',
            'id'
        );
    }
    /**
     * Kondisi barang
     */
    public function kondisi()
    {
        return $this->belongsTo(
            Kondisi::class,
            'id_kondisi',
            'id'
        );
    }
    /**
     * User pemilik transaksi
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }
    /**
     * Umur tanaman
     */
    public function getUmurTanamanAttribute()
    {
        $startDate = $this->tanggal_masuk
            ?? $this->created_at
            ?? now();
        $diffDays = (int) Carbon::parse($startDate)
            ->diffInDays(now());
        if ($diffDays <= 0) {
            return 'Baru Ditanam (1 Hari)';
        }
        if ($diffDays < 30) {
            return $diffDays . ' Hari';
        }
        if ($diffDays < 365) {
            $months = floor($diffDays / 30);
            $remainingDays = $diffDays % 30;
            return $months . ' Bulan' .
                ($remainingDays > 0
                    ? ' ' . $remainingDays . ' Hari'
                    : '');
        }
        $years = floor($diffDays / 365);
        return $years . ' Tahun';
    }
}
