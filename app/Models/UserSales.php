<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserSales extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'birthdate', 'gender', 'address', 
        'verification_code', 'kode_unik', 'kode_sales', 'merk_hp', 'profile_photo', // Added profile_photo here
        'phone_verified_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date',
        'phone_verified_at' => 'datetime',
    ];

    public function resellers()
    {
        return $this->hasMany(Reseller::class);
    }
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('uploads/profile_photos/' . $this->profile_photo);
        }
        return null;
    }
    // Accessor to get the full URL for the profile photo
}
