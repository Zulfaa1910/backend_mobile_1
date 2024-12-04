<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birthdate',
        'gender',
        'address',
        'verification_code',
        'kode_unik',
        'kode_sales',  // Hapus spasi di sini
        'merk_hp',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date',
        'phone_verified_at' => 'datetime',
    ];

    public function resellers()
    {
        return $this->hasMany(Reseller::class); // User memiliki banyak reseller
    }

    public function visits()
    {
        return $this->hasMany(Visit::class); // User memiliki banyak reseller
    }
}
