<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'data',
        'message',
        'audit_added_date',
        'audit_added_time',
        'auditable_id',
        'auditable_type',
    ];

    public function auditable()
    {
        return $this->morphTo();
    }
}
