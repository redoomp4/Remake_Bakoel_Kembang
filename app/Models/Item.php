<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\User;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';


    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'kode_barang',
        'public_token',
        'nama_barang',
        'deskripsi',
        'foto',
        'id_kategori',
        'id_satuan',
        'stok_minimum',
        'harga_dasar',
        'qr_code',
    ];


    /*
    |--------------------------------------------------------------------------
    | BOOT MODEL
    |--------------------------------------------------------------------------
    |
    | public_token dibuat otomatis ketika Item dibuat.
    |
    */

    protected static function booted()
    {
        static::creating(function ($item) {

            if (empty($item->public_token)) {
                $item->public_token = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | BARANG MASUK
    |--------------------------------------------------------------------------
    */

    public function barangMasuk()
    {
        return $this->hasMany(
            BarangMasuk::class,
            'item_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BARANG KELUAR
    |--------------------------------------------------------------------------
    */

    public function barangKeluar()
    {
        return $this->hasMany(
            BarangKeluar::class,
            'item_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KATEGORI
    |--------------------------------------------------------------------------
    */

    public function kategori()
    {
        return $this->belongsTo(
            Kategori::class,
            'id_kategori',
            'id'
        );
    }

    public function satuan()
    {
        return $this->belongsTo(
            Satuan::class,
            'id_satuan',
            'id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    public function getTotalStokAttribute()
    {
        $totalIn = (int) $this->barangMasuk()->sum('jumlah');

        $totalOut = (int) $this->barangKeluar()->sum('jumlah_keluar');

        return max(0, $totalIn - $totalOut);
    }

    public function getUmurTanamanAttribute()
    {
        $firstEntry = $this->barangMasuk()
            ->oldest('tanggal_masuk')
            ->first();

        $startDate = $firstEntry->tanggal_masuk
            ?? $this->created_at
            ?? now();

        $diffDays = (int) \Carbon\Carbon::parse($startDate)
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

        return floor($diffDays / 365) . ' Tahun';
    }

    public static function generateKodeBarang()
    {
        $userId = auth()->id();

        $latest = self::where('user_id', $userId)
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return 'BRG-001';
        }

        $number = (int) str_replace(
            'BRG-',
            '',
            $latest->kode_barang
        );

        return 'BRG-' .
            str_pad(
                $number + 1,
                3,
                '0',
                STR_PAD_LEFT
            );
    }


    public static function getKategoriOptions()
    {
        return DB::table('items')
            ->join(
                'kategoris',
                'items.id_kategori',
                '=',
                'kategoris.id'
            )
            ->where(
                'items.user_id',
                auth()->id()
            )
            ->select('kategoris.kategori')
            ->distinct()
            ->orderBy('kategoris.kategori')
            ->pluck('kategoris.kategori')
            ->toArray();
    }
}
