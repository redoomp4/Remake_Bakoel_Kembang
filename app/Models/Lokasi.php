<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\User;

class Lokasi extends Model
{
    use HasFactory;


    protected $fillable = [
        'nama_lokasi',
        'deskripsi',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'id_lokasi');
    }


    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class, 'id_lokasi');
    }
}
