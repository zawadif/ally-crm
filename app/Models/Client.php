<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

//    protected $dateFormat = 'U';
//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//    const DELETED_AT = 'deletedAt';
    protected $fillable = [
        'app_name',
        'app_email',
        'app_phone',
        'app_phoneHome',
        'app_job_title',
        'app_job_category',
        'app_source',
        'app_status','paid_timestamp',
        'app_postcode',
        'app_lat',
        'app_long',
        'user_id',
        'applicant_added_time','applicant_added_date',
        'applicant_cv',
        'applicant_update_cv','paid_status',
        'app_job_title_prof','temp_not_interested','is_blocked','is_no_job',
        'applicant_notes','created_at','updated_at','no_response',
        'is_interview_confirm','is_cv_in_quality_clear','is_cv_in_quality','is_CV_reject',
        //new fields migrate pending
        'is_in_crm_request','is_in_crm_reject','is_in_crm_request_reject','is_crm_request_confirm',
        'is_crm_interview_attended','is_in_crm_start_date','is_in_crm_invoice','is_in_crm_start_date_hold',
        'is_in_crm_paid','is_callback_enable','is_in_nurse_home','is_in_crm_dispute','is_in_crm_invoice_sent'
        // Exclude timestamps and soft deletes as they're managed by Laravel
        // 'createdAt',
        // 'updatedAt',
        // 'deletedAt',
    ];

    /**
     * Get all audits associated with the applicant.
     */
    public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
    public function cv_notes()
    {
        return $this->hasMany(CvNote::class)->select('status', 'client_id', 'sale_id');
    }
    public function applicants_pivot_sales()
    {
        return $this->hasMany(Applicants_pivot_sales::class, 'client_id');
    }

    public function callback_notes()
    {
        return $this->hasMany(ApplicantNote::class)->whereIn('moved_tab_to', ['callback','revert_callback'])->orderBy('id', 'desc');
    }

    /**
     * Get the no_nursing_home_notes for the applicant.
     */
    public function no_nursing_home_notes()
    {
        return $this->hasMany(ApplicantNote::class)->whereIn('moved_tab_to', ['no_nursing_home','revert_no_nursing_home'])->orderBy('id', 'desc');
    }
}
