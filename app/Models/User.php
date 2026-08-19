<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
        'role', 'position', 'phone', 'status', 'photo', 'note', 'is_active', 'last_login'
    ];

    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login' => 'datetime', 
    ];

    public function barangMasuk()
    {
        return $this->hasMany(\App\Models\BarangMasuk::class);
    }

    public function barangKeluar()
    {
        return $this->hasMany(\App\Models\BarangKeluar::class);
    }

    /**
     * Kirim notifikasi verifikasi email khusus dengan template HTML ramah anti-spam.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmailNotification());
    }
}
