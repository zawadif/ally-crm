<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'head_office',
        'unit_name',
        'unit_postcode',
        'contact_name',
        'contact_phone_number',
        'contact_landline',
        'contact_email',
        'website',
        'status',
        'unit_added_time','unit_added_date',
        'unit_notes'
    ];
    public function headOffice()
    {
        return $this->belongsTo(Office::class, 'head_office');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function module_notes()
    {
        return $this->morphMany(ModuleNote::class, 'module_noteable');
    }

}
