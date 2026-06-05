<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id', 'batch_number', 'quantity', 'initial_quantity',
        'expiry_date', 'purchase_price', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'received_at' => 'date',
            'purchase_price' => 'decimal:2',
        ];
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
