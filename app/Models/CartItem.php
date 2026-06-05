<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'medicine_id', 'quantity', 'price_snapshot'];

    protected function casts(): array
    {
        return ['price_snapshot' => 'decimal:2'];
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->price_snapshot * $this->quantity;
    }
}
