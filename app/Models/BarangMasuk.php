<?php




namespace App\Models;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class BarangMasuk extends Model
{
    use HasFactory;




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
        'qr_code',
        'catatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'tanggal_kadaluarsa' => 'date',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    // Accessor Kode Barang untuk fallback kompatibilitas view
    public function getKodeBarangAttribute()
    {
        return $this->item?->kode_barang ?? '-';
    }

    // Accessor Umur Tanaman (dihitung dari tanggal_masuk/created_at hingga sekarang)
    public function getUmurTanamanAttribute()
    {
        $startDate = $this->tanggal_masuk ?? $this->created_at ?? now();
        $diffDays = (int) \Carbon\Carbon::parse($startDate)->diffInDays(now());

        if ($diffDays <= 0) {
            return 'Baru Ditanam (1 Hari)';
        } elseif ($diffDays < 30) {
            return $diffDays . ' Hari';
        } elseif ($diffDays < 365) {
            $months = floor($diffDays / 30);
            $remainingDays = $diffDays % 30;
            return $months . ' Bulan' . ($remainingDays > 0 ? ' ' . $remainingDays . ' Hari' : '');
        } else {
            $years = floor($diffDays / 365);
            return $years . ' Tahun';
        }
    }

    // Relasi ke model Item berdasarkan item_id
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }




    // Relasi ke model Pemasok
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }

    public function kategori()
    {
        return $this->belongsTo(\App\Models\Kategori::class, 'id_kategori'); // sesuaikan FK
    }



    // Relasi ke model Lokasi
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }




    // Relasi ke model Kondisi
    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi');
    }




    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
