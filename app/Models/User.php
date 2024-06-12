<?php

namespace App\Models;

use AWS\CRT\HTTP\Request;
use Carbon\Carbon;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes, Billable;
    protected $dateFormat = 'U';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullName',
        'email',
        'phoneNumber',
        'password',
        'isVerified',
        'profileUrl',
        'status',
        'is_admin'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $appends = ['formatDate','formatTime'];


    public function getDateFormat()
    {
        return 'U';
    }

    public function getformatDateAttribute(){
        $date=Carbon::parse($this->created_at)->format('d M Y');
        return $date;
    }
    public function getformatTimeAttribute(){
        $time=Carbon::parse($this->created_at)->format('h:i A');
        return $time;
    }
    public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }








}
