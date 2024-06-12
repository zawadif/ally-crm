<!-- Sales -->
@if(in_array($user_role, ['Sales', 'Sale and CRM']))

    <div class="card-header bg-primary text-white">
        <h5 class="card-title">Sales</h5>
        <div>
            <h6 class="font-weight-bold">Role: {{ ucwords($user_role) }}</h6>
        </div>
    </div>

    <div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap" style="padding-bottom: 15px; !important;">

        <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
            <div>
                <i class="fas fa-door-open text-orange-800" style="font-size: 30px;"></i>
            </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0">{{ number_format($user_stats['open_sales']) }}</h6>
                <span class="text-muted">Open</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
            <div>
                <i class="fa fa-door-closed text-danger-400" style="font-size: 30px;"></i>
            </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0">{{ number_format($user_stats['close_sales']) }}</h6>
                <span class="text-muted">Close</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
            <div>
                <i class="fas fa-building text-primary-400" style="font-size: 30px;"></i>
            </div>
            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0">{{ number_format($user_stats['psl_offices']) }}</h6>
                <span class="text-muted">PSL Office</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
            <div>
                <i class="far fa-building text-blue-400" style="font-size: 30px;"></i>
            </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0">{{ number_format($user_stats['non_psl_offices']) }}</h6>
                <span class="text-muted">NON PSL Office</span>
            </div>
        </div>
    </div>

@endif

<!-- Quality -->
<div class="card-header header-elements-sm-inline d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Quality</h5>
    @if(($user_role != 'Sales') && ($user_role != 'Sale and CRM'))
        <div class="ml-3">
            <h6 class="font-weight-bold mb-0 text-muted">Role: {{ $user_role }}</h6>
        </div>
    @endif
</div>


<div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap" style="padding-bottom: 15px;">
    <div class="col-md-4 d-flex align-items-center mb-3 mb-md-0">
        <div>
            <i class="fas fa-file-alt text-primary" style="font-size: 36px;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-bold mb-0" id="custom_quality_cvs">{{ number_format($user_stats['no_of_send_cvs_from_cv_notes']) }}</h6>
            <span class="text-muted">CVs (Sent)</span>
        </div>
    </div>

    <div class="col-md-4 d-flex align-items-center mb-3 mb-md-0">
        <div>
            <i class="fa fa-ban text-danger" style="font-size: 36px;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-bold mb-0" id="custom_quality_rejected">{{ number_format($user_stats['cvs_rejected']) }}</h6>
            <span class="text-muted">CVs Rejected</span>
        </div>
    </div>

    <div class="col-md-4 d-flex align-items-center mb-3 mb-md-0">
        <div>
            <i class="fa fa-check-circle text-success" style="font-size: 36px;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-bold mb-0" id="custom_quality_cleared">{{ number_format($user_stats['cvs_cleared']) }}</h6>
            <span class="text-muted">CVs Cleared</span>
        </div>
    </div>
</div>
<!-- /quality -->

<!-- CRM -->
<div class="card-header d-flex align-items-center justify-content-between bg-light border-bottom">
    <h5 class="card-title mb-0">Clients in CRM Stages (4)</h5>
</div>


<div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap" style="padding-bottom: 0 !important;">

    <div class="card-body d-flex flex-wrap justify-content-between align-items-center" style="padding-bottom: 0 !important;">
        <div class="col-md-3 d-flex align-items-center mb-2">
            <i class="fas fa-file-alt mr-2 text-primary" style="font-size: 25px;"></i>
            <div>
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_open_sales">{{ $user_stats['crm_sent_cvs'] }}</h6>
                <span class="text-muted">Sent CVs</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-2">
            <i class="fas fa-times-circle mr-2 text-danger" style="font-size: 30px;"></i>
            <div>
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_close_sales">{{ $user_stats['crm_rejected_cv'] }}</h6>
                <span class="text-muted">Rejected CVs</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-2">
            <i class="fas fa-envelope mr-2 text-warning" style="font-size: 30px;"></i>
            <div>
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_psl">{{ $user_stats['crm_request'] }}</h6>
                <span class="text-muted">Requests</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-2">
            <i class="fas fa-ban mr-2 text-secondary" style="font-size: 30px;"></i>
            <div>
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_rejected_by_request'] }}</h6>
                <span class="text-muted">Rejected by Request</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-2">
            <i class="fas fa-check-circle mr-2 text-success" style="font-size: 30px;"></i>
            <div>
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_confirmation'] }}</h6>
                <span class="text-muted">Confirmation</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-2">
            <i class="fas fa-redo mr-2 text-info" style="font-size: 30px;"></i>
            <div>
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_rebook'] }}</h6>
                <span class="text-muted">Rebook</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-2">
            <i class="fas fa-user-check mr-2 text-primary" style="font-size: 30px;"></i>
            <div>
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_attended'] }}</h6>
                <span class="text-muted">Attended</span>
            </div>
        </div>





        <div class="col-md-3 d-flex align-items-center mb-md-2">
            <div>
                <i class="fas fa-user-times mr-2 text-danger" style="font-size: 30px;"></i>  </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_not_attended'] }}</h6>
                <span class="text-muted">Not Attended</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-md-2">
            <div>
                <i class="fas fa-calendar-check mr-2 text-primary" style="font-size: 30px;"></i>  </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_start_date'] }}</h6>
                <span class="text-muted">Start Date Confirmed</span>  </div>
        </div>



        <div class="col-md-3 d-flex align-items-center mb-md-2">
            <div>
                <i class="fas fa-calendar mr-2 text-warning" style="font-size: 30px;"></i>
            </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_start_date_hold'] }}</h6>
                <span class="text-muted">Start Date Hold</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-md-2">
            <div>
                <i class="fas fa-file-invoice mr-2 text-info" style="font-size: 30px;"></i>  </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_invoice'] }}</h6>
                <span class="text-muted">Invoice</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-md-2">
            <div>
                <i class="fas fa-thumbs-down mr-2 text-danger" style="font-size: 30px;"></i>  </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_declined'] }}</h6>
                <span class="text-muted">Declined</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-md-2">
            <div>
                <i class="fas fa-exclamation-triangle mr-2 text-warning" style="font-size: 30px;"></i>  </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_dispute'] }}</h6>
                <span class="text-muted">Dispute</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-md-2">
            <div>
                <i class="fas fa-money-bill-wave mr-2 text-success" style="font-size: 30px;"></i>  </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_stats['crm_paid'] }}</h6>
                <span class="text-muted">Paid</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-md-2">
    </div>
    <div class="col-md-3 d-flex align-items-center mb-md-2">
    </div>

</div><br>
<!-- /crm -->
    <div class="card d-flex flex-column">  <div class="card-header header-elements-sm-inline d-flex justify-content-between" style="padding-top: 0 !important; padding-bottom: 5px !important;">
            <h5 class="card-title mb-0">Last Month Applicant's Statistics</h5>
        </div>
        <div class="card-body d-flex flex-wrap justify-content-between" style="padding-bottom: 0 !important;">
            <div class="col-md-3 d-flex align-items-center mb-md-2">
                <div>
                    <i class="fas fa-calendar-check mr-2 text-primary" style="font-size: 30px;"></i>  </div>
                <div class="ml-3">
                    <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $prev_user_stats['crm_start_date'] }}</h6>
                    <span class="text-muted">Start Date</span>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-center mb-md-2">
                <div>
                    <i class="fas fa-file-invoice mr-2 text-info" style="font-size: 30px;"></i>  </div>
                <div class="ml-3">
                    <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $prev_user_stats['crm_invoice'] }}</h6>
                    <span class="text-muted">Invoice</span>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-center mb-md-2">
                <div>
                    <i class="fas fa-money-bill-wave mr-2 text-success" style="font-size: 30px;"></i>  </div>
                <div class="ml-3">
                    <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $prev_user_stats['crm_paid'] }}</h6>
                    <span class="text-muted">Paid</span>
                </div>
            </div>
        </div>
    </div>
</div>


    {{-- <div><hr></div>
    <div class="modal-header">
        <h5 class="modal-title text-orange-800">
            <span class="font-weight-semibold" id="user_name">{{$user_all_stats['username']}}</span>'s All Statistics
        </h5>
    </div>
    @if(in_array($user_role, ['Sales', 'Sale and CRM']))

        <div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap" style="padding-bottom: 15px; !important;">

            <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
                <div>
                    <i class="fas fa-door-open text-orange-800" style="font-size: 30px;"></i>
                </div>

                <div class="ml-3">
                    <h6 class="font-weight-semibold mb-0">{{ number_format($user_all_stats['open_sales']) }}</h6>
                    <span class="text-muted">Open</span>
                </div>
            </div>

            <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
                <div>
                    <i class="fa fa-door-closed text-danger-400" style="font-size: 30px;"></i>
                </div>

                <div class="ml-3">
                    <h6 class="font-weight-semibold mb-0">{{ number_format($user_all_stats['close_sales']) }}</h6>
                    <span class="text-muted">Close</span>
                </div>
            </div>

            <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
                <div>
                    <i class="fas fa-building text-primary-400" style="font-size: 30px;"></i>
                </div>
                <div class="ml-3">
                    <h6 class="font-weight-semibold mb-0">{{ number_format($user_all_stats['psl_offices']) }}</h6>
                    <span class="text-muted">PSL Office</span>
                </div>
            </div>

            <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
                <div>
                    <i class="far fa-building text-blue-400" style="font-size: 30px;"></i>
                </div>

                <div class="ml-3">
                    <h6 class="font-weight-semibold mb-0">{{ number_format($user_all_stats['non_psl_offices']) }}</h6>
                    <span class="text-muted">NON PSL Office</span>
                </div>
            </div>
        </div>

    @endif
    <div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap" style="padding-bottom: 15px; !important;">
        <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
            <div>
                <i class="fas fa-file-alt text-blue-400" style="font-size: 30px;"></i>
            </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="custom_quality_cvs">{{ number_format($user_all_stats['all_no_of_send_cvs_from_cv_notes']) }}</h6>
                <span class="text-muted">CVs (Sent)</span>
            </div>
        </div>

        <div class="col-md-4 d-flex align-items-center mb-3 mb-md-0">
            <div>
                <i class="fa fa-ban text-danger-400" style="font-size: 30px;"></i>
            </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="custom_quality_rejected">{{ number_format($user_all_stats['all_cvs_rejected']) }}</h6>
                <span class="text-muted">CVs Rejected</span>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
            <div>
                <i class="fa fa-clipboard-check text-teal-400" style="font-size: 30px;"></i>
            </div>

            <div class="ml-3">
                <h6 class="font-weight-semibold mb-0" id="custom_quality_cleared">{{ number_format($user_all_stats['all_cvs_cleared']) }}</h6>
                <span class="text-muted">CVs &nbsp;Cleared</span>
            </div>
        </div>

    </div> --}}
<!-- /quality -->

<!-- CRM -->
{{-- <div class="card-header header-elements-sm-inline" style="padding-top: 0 !important; padding-bottom: 5px !important;">
    <h5 class="card-title">Applicants in CRM Stages (total_applicants)</h5>
</div>

<div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap" style="padding-bottom: 0 !important;">

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #b45100;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_open_sales">{{ $user_all_stats['all_crm_sent_cvs'] }}</h6>
            <span class="text-muted">Sent CVs</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fas fa-file-excel" style="font-size: 30px; color: #c85a00;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_close_sales">{{ $user_all_stats['all_crm_rejected_cv'] }}</h6>
            <span class="text-muted">Rejected CV</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #db6300;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_psl">{{ $user_all_stats['all_crm_request'] }}</h6>
            <span class="text-muted">Request</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fas fa-file-excel" style="font-size: 30px; color: #ef6c00;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_rejected_by_request'] }}</h6>
            <span class="text-muted">Rejected by Request</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #ff7504;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_confirmation'] }}</h6>
            <span class="text-muted">Confirmation</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #ff8017;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_rebook'] }}</h6>
            <span class="text-muted">Rebook</span>
        </div>
    </div>
    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #ff8017;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_attended'] }}</h6>
            <span class="text-muted">Pre-Start Date</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fas fa-file-excel" style="font-size: 30px; color: #ff8b2b;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_not_attended'] }}</h6>
            <span class="text-muted">Not Attended</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #ff953e;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_start_date'] }}</h6>
            <span class="text-muted">Start Date</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fas fa-file-excel" style="font-size: 30px; color: #ffa052;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_start_date_hold'] }}</h6>
            <span class="text-muted">Start Date Hold</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #ffab66;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_invoice'] }}</h6>
            <span class="text-muted">Invoice</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fas fa-file-excel" style="font-size: 30px; color: #fab073;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_declined'] }}</h6>
            <span class="text-muted">Declined</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fas fa-file-excel" style="font-size: 30px; color: #f8b57d;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_dispute'] }}</h6>
            <span class="text-muted">Dispute</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
        <div>
            <i class="fa fa-clipboard-check" style="font-size: 30px; color: #fcc598;"></i>
        </div>

        <div class="ml-3">
            <h6 class="font-weight-semibold mb-0" id="monthly_no_of_nonpsl">{{ $user_all_stats['all_crm_paid'] }}</h6>
            <span class="text-muted">Paid</span>
        </div>
    </div>

    <div class="col-md-3 d-flex align-items-center mb-md-2">
    </div>
    <div class="col-md-3 d-flex align-items-center mb-md-2">
    </div>
</div> --}}
<!-- /crm -->
