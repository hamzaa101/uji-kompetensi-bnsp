<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = ['severity', 'message', 'file', 'line', 'trace', 'is_resolved', 'resolved_at'];

    protected function casts(): array
    {
        return ['is_resolved' => 'boolean', 'resolved_at' => 'datetime'];
    }
}
