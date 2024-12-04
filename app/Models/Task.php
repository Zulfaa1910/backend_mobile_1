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
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
