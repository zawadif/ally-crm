<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'client_id',
        'sale_id',
        'details',
        'moved_tab_to',
        'crm_added_date',
        'crm_added_time'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
