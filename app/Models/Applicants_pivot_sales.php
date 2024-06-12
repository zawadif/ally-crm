<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicants_pivot_sales extends Model
{
    use HasFactory;
    protected $fillable=[
        'interest_added_date','interest_added_time','client_id','sale_id','status','is_interested','details'
    ];
}
