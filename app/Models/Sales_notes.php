<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales_notes extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'sale_id',
        'sale_note',
        'sales_note_added_date',
        'sales_note_added_time',
        'status','type_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
