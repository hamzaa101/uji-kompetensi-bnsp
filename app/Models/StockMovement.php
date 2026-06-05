<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'medicine_id', 'medicine_batch_id', 'order_id', 'type',
        'quantity', 'description', 'created_by',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function batch()
    {
        return $this->belongsTo(MedicineBatch::class, 'medicine_batch_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
