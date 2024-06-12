<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'module_note_uid', 'user_id', 'module_noteable_id', 'module_noteable_type', 'details', 'module_note_added_date', 'module_note_added_time', 'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
