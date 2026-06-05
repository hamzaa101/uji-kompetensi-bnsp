<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'table_name', 'record_id', 'ip_address', 'user_agent', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
