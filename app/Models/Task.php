<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task',
        'description',
        'reseller_id',
        'user_sales_id',
        'assigned_to',
        'status',
        'photo_url',
        'deadline',
        'upload_time',
        'input_source', // Tambahkan ini
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
