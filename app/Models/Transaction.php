<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'snap_token',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
