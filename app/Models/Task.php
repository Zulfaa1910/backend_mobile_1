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
        'assigned_to', // Can be warehouse, maintenance, reseller, etc.
        'status',       // Pending or completed
        'photo_url',    // URL to the photo or base64 string
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
