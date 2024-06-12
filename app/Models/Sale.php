<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'head_office', 'head_office_unit', 'job_category', 'job_title',
        'postcode', 'job_type', 'time', 'salary', 'experience', 'qualification',
        'benefits', 'lat', 'lng', 'status', 'sale_added_time', 'sale_added_date',
        'job_title_prof', 'send_cv_limit', 'sale_notes','is_on_hold','posted_date'
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'head_office');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'head_office_unit');
    }
    public function active_cvs()
    {
        return $this->hasMany(CvNote::class, 'sale_id', 'id')->where('status', '=', 'active');
    }
}
