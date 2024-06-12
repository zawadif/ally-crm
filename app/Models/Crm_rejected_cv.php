<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_rejected_cv extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'sale_id',
        'client_id',
        'crm_note_id',
        'crm_rejected_cv_note',
        'crm_rejected_cv_date',
        'crm_rejected_cv_time',
        'status'
    ];
}
