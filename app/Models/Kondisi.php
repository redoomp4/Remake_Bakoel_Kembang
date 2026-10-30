<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kondisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kondisi', 'deskripsi', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'id_kondisi');
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class, 'id_kondisi');
    }
}
