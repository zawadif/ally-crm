<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvNote extends Model
{
    use HasFactory;
    protected $fillable=[
      'user_id','client_id','sale_id','status','details','send_added_date','send_added_time'
    ];
}
