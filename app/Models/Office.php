<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;
    protected $fillable=[
        'office_added_date','office_added_time','name','postcode','type','email',
        'contact_number','contact_landline','office_notes','status','lat','long','user_id','website'
    ];

    /**
     * Get all audits associated with the office.
     */
    public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function module_notes()
    {
        return $this->morphMany(ModuleNote::class, 'module_noteable');
    }
}
