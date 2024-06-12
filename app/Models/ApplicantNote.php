<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'moved_tab_to',
        'status',
        'added_date',
        'added_time',
        'details',
    ];
}
