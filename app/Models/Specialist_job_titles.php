<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist_job_titles extends Model
{
    use HasFactory;
    protected $fillable=[
        'special_type','name','created_at','updated_at'
    ];
}
