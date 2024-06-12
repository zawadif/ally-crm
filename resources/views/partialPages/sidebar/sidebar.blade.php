  @php
        use App\Helpers\Helper;
        $customHelper =  new Helper;
    @endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('dashboard') }}" class="brand-link" style="border: none;padding-bottom: 0px;">
        <h3 class="p-2 text-white">Recruitment Ally</h3>
    </a>
    <hr style="color: white !important; border-top: 1px solid;" class="ml-2 mr-2">

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <ul class="products-list product-list-in-card pl-2 pr-2">
            <li class="item pt-0 pb-0" style="background-color: var(--baseColor)">
                <div class="product-img">

                        <img src="{{ asset('img/avatar/default-avatar.png') }}" alt="User Image" class="img-size-50"
                            style="height: 41px !important;width: 41px !important;border-radius: 50%;">

                </div>
                <div class="product-info pl-3"><a href="#">
                        <small href="javascript:void(0)"
                            class="product-title userNameText">{{ auth()->user()->fullName ? auth()->user()->fullName : 'Administrator' }}
                    </a>
                    <div class="dropdown dropdown-block-aside" id="checkplaylis" style="float: right !important">
                        <i class="" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="bi bi-three-dots-vertical" style="font-size: 30px"></i></i>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                <a class="dropdown-item btn-block-aside text-dark"
                                    href="#">Manage administrators</a>

                            <form action="{{ route('logout') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <button class="btn btn-block  btn-sm rounded"
                                    style="text-align: left;padding-left: 24px;
                                    font-size: 15px;
                                    font-weight: 500;
                                ">Logout</button>
                            </form>
                        </div>
                    </div>
                    </small>
                    <span class="product-description userNameText">
                        <a href="#">
                            @role('admin')
                                @if (auth()->user()->roles->first())
                                    {{ ucfirst(auth()->user()->roles->first()->name) }}
                                @else
                                    {{ '' }}
                                @endif
{{--                            --}}
{{--                                @if (auth()->user()->roles->skip(1)->first())--}}
{{--                                    {{ ucfirst(auth()->user()->roles->skip(1)->first()->name) }}--}}
{{--                                @else--}}
{{--                                    {{ '' }}--}}
{{--                                @endif--}}
                            @endrole
                        </a>
                    </span>
                </div>
            </li>
        </ul>
    </div>
    <hr style="color: white !important; border-top: 1px solid;" class="ml-2 mr-2">
    <div class="sidebar px-0">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ url('dashboard') }}"
                        class="nav-link {{ request()->is('/dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fas fa-th"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                   @can('user_list')
                    <li class="nav-item">
                        <a href="{{ url('users') }}"
                            class="nav-link {{ request()->is('users/*') ? 'active' : '' }}" >
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Teams
                            </p>
                        </a>
                    </li>
                @endcan
                @canany(['applicant_list','applicant_import','applicant_create','applicant_edit','applicant_view','applicant_history','applicant_note-create','applicant_note-history'])
                    <li class="nav-item">
                        <a href="{{ route('clients.index') }}"
                           class="nav-link {{ request()->is('clients.index/*') ? 'active' : '' }}" >
                            <i class="nav-icon fas fa-user-nurse"></i>
                            <p>
                               Clients
                            </p>
                        </a>
                    </li>
                @endcanany
                @canany(['office_list','office_import','office_create','office_edit','office_view','office_note-history','office_note-create'])

                    <li class="nav-item">
                        <a href="{{ route('offices.index') }}"
                           class="nav-link {{ request()->is('offices.index/*') ? 'active' : '' }}" >
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                               Franchises
                            </p>
                        </a>
                    </li>
                @endcanany
                    @canany(['unit_list','unit_import','unit_create','unit_edit','unit_view','unit_note-create','unit_note-history'])
                    <li class="nav-item">
                        <a href="{{ route('units.index') }}"
                           class="nav-link {{ request()->is('units.index/*') ? 'active' : '' }}" >
                            <i class="nav-icon fa fa-balance-scale"></i>
                            <p>
                               Unit
                            </p>
                        </a>
                    </li>
                @endcanany
                @canany(['sale_list','sale_import','sale_create','sale_edit','sale_view','sale_open','sale_close','sale_manager-detail','sale_history','sale_notes','sale_note-create','sale_note-history','sale_closed-sales-list','sale_closed-sale-notes','sale_psl-offices-list','sale_psl-office-details','sale_psl-office-units','sale_non-psl-offices-list','sale_non-psl-office-details','sale_non-psl-office-units'])
                    <li class="nav-item {{ request()->is('users/*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('users') ? 'menu-open' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Sales
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['sale_list','sale_import','sale_create','sale_edit','sale_view','sale_close','sale_manager-detail','sale_history','sale_notes','sale_note-create','sale_note-history'])

                            <li class="nav-item">
                                <a href="{{route('sales.index')}}"
                                   class="pl-5 nav-link {{ request()->is('sales.index/active') ? 'active' : '' }}">
                                    <i class=""></i>
                                    <p>Accept</p>
                                </a>
                            </li>
                            @endcanany
                             @canany(['sale_closed-sales-list','sale_open','sale_closed-sale-notes'])

                            <li class="nav-item">
                                <a href="{{ route('close_sales') }}"
                                   class="pl-5 nav-link {{ request()->is('close_sales/active') ? 'active' : '' }}">
                                    <i class=""></i>
                                    <p>Disable</p>
                                </a>
                            </li>
                            @endcanany
                                @canany(['sale_on-hold'])
{{--                            <li class="nav-item">--}}
{{--                                <a href="#"--}}
{{--                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">--}}
{{--                                    <i class=""></i>--}}
{{--                                    <p>On Hold Sales</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                            @endcanany
                                @canany(['sale_psl-offices-list','sale_psl-office-details','sale_psl-office-units'])

{{--                            <li class="nav-item">--}}
{{--                                <a href="#"--}}
{{--                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">--}}
{{--                                    <i class=""></i>--}}
{{--                                    <p>PSL</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                            @endcanany
                                @canany(['sale_non-psl-offices-list','sale_non-psl-office-details','sale_non-psl-office-units'])

{{--                            <li class="nav-item">--}}
{{--                                <a href="#"--}}
{{--                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">--}}
{{--                                    <i class=""></i>--}}
{{--                                    <p>NON PSL</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                            @endcanany

                        </ul>
                    </li>

                @endcanany
                @canany(['quality_CVs_list','quality_CVs_cv-download','quality_CVs_job-detail','quality_CVs_cv-clear','quality_CVs_cv-reject','quality_CVs_manager-detail','quality_CVs-Rejected_list','quality_CVs-Rejected_job-detail','quality_CVs-Rejected_cv-download','quality_CVs-Rejected_manager-detail','quality_CVs-Rejected_revert-quality-cv','quality_CVs-Cleared_list','quality_CVs-Cleared_job-detail','quality_CVs-Cleared_cv-download','quality_CVs-Cleared_manager-detail'])
                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link {{ request()->is('users') ? 'menu-open' : '' }}">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>
                                Quality
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        {{--                        <a href="#" class="nav-link"><i class="icon-medal"></i> <span></span></a>--}}
                        <ul class="nav nav-treeview" data-submenu-title="Quality">

                            @canany(['quality_Sales_list','quality_Sales_sale-clear','quality_Sales_sale-reject','quality_Sales-Cleared_list','quality_Sales-Rejected_list'])
                                <li class="nav-item nav-item-submenu">
                                    {{--                                    <a href="#" class="nav-link">--}}
                                    <a href="#"
                                       class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                        <i class="fa fa-arrow-circle-right"></i>
                                        <p>Cvs</p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @canany(['quality_CVs_list','quality_CVs_cv-download','quality_CVs_job-detail','quality_CVs_cv-clear','quality_CVs_cv-reject','quality_CVs_manager-detail'])
                                            <li class="nav-item">
                                                <a href="{{ route('applicantWithSentCv') }}"
                                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                                    <i class="fas fa-file"></i>
                                                    <p>Active Cvs</p>
                                                </a>
                                            </li>
                                        @endcanany
                                        @canany(['quality_CVs-Rejected_list','quality_CVs-Rejected_job-detail','quality_CVs-Rejected_cv-download','quality_CVs-Rejected_manager-detail','quality_CVs-Rejected_revert-quality-cv'])

                                            <li class="nav-item">
                                                <a href="{{ route('applicantWithRejectedCV') }}"
                                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                                    <i class="fas fa-times-circle"></i>
                                                    <p>Cvs Decline</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @canany(['quality_CVs-Cleared_list','quality_CVs-Cleared_job-detail','quality_CVs-Cleared_cv-download','quality_CVs-Cleared_manager-detail'])

                                            <li class="nav-item">
                                                <a href="{{ route('applicantsWithConfirmedInterview') }}"
                                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                                    <i class="fas fa-check-circle"></i>
                                                    <p>Cvs Accept</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                                <li class="nav-item nav-item-submenu">
                                    {{--                                    <a href="#" class="nav-link">--}}
                                    <a href="#"
                                       class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                        <i class="fa fa-arrow-circle-right"></i>
                                        <p>Sale</p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @canany(['quality_Sales_list','quality_Sales_sale-clear','quality_Sales_sale-reject'])
                                            <li class="nav-item">
                                                <a href="{{ route('quality-sales') }}"
                                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                                    <i class="fas fa-dollar-sign"></i>
                                                    <p>Pending Sale</p>
                                                </a>
                                            </li>
                                            {{--                                            <li class="nav-item"><a href="#" class="nav-link">Sales</a></li>--}}
                                        @endcanany
                                        @can('quality_Sales-Cleared_list')
                                            <li class="nav-item">
                                                <a href="{{ route('quality-sales-cleared') }}"
                                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                                    <i class="fas fa-check"></i>
                                                    <p>Sales Accept</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('quality_Sales-Rejected_list')
                                            <li class="nav-item">
                                                <a href="{{ route('quality-sales-rejected') }}"
                                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                                    <i class="fas fa-times"></i>
                                                    <p>Sales Decline</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
                @canany(['resource_Nurses-list','resource_Non-Nurses-list','resource_Last-7-Days-Applicants','resource_Last-21-Days-Applicants','resource_All-Applicants','resource_Crm-Rejected-Applicants','resource_Crm-Request-Rejected-Applicants','resource_Crm-Not-Attended-Applicants','resource_Crm-Start-Date-Hold-Applicants','resource_No-Nursing-Home_list','resource_No-Nursing-Home_revert-no-nursing-home','resource_Non-Interested-Applicants','resource_Crm-Paid-Applicants','resource_Potential-Callback_list','resource_Potential-Callback_revert-callback'])

                    <li class="nav-item {{ request()->is('users/*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('users') ? 'menu-open' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Resource
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            @can('resource_Nurses-list')
                                <li class="nav-item">
                                    <a href="{{ route('getDirectNurse') }}"
                                       class="pl-5 nav-link {{ request()->is('sales/active') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-user-nurse"></i>
                                        <p>Qualified Staff</p>
                                    </a>
                                </li>
                            @endcan

                            @can('resource_Non-Nurses-list')
                                <li class="nav-item">
                                    <a href="{{ route('getDirectNonNurse') }}"
                                       class="pl-5 nav-link {{ request()->is('getDirectNonNurse/active') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-user-alt-slash"></i>
                                        <p>Non Qualified Staff</p>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a href="{{ route('getDirectNonNurseSpecialist') }}"
                                   class="pl-5 nav-link {{ request()->is('getDirectNonNurseSpecialist/active') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-md"></i>
                                    <p>Specialist Staff</p>
                                </a>
                            </li>

                            <li class="nav-item nav-item-submenu">
                                <a href="#"
                                   class="pl-5 nav-link {{ request()->is('users/active') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-arrow-circle-right"></i>
                                    <p>Clients</p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @can('resource_Last-7-Days-Applicants')
                                        <li class="nav-item">
                                            <a href="{{ route('last7days') }}"
                                               class="pl-5 nav-link {{ request()->is('last7days/active') ? 'active' : '' }}">
                                                <i class="nav-icon far fa-calendar-alt"></i>
                                                <p>Last 14days clients</p>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('resource_Last-21-Days-Applicants')
                                        <li class="nav-item">
                                            <a href="{{ route('last21days') }}"
                                               class="pl-5 nav-link {{ request()->is('last21days/active') ? 'active' : '' }}">
                                                <i class="nav-icon far fa-calendar-alt"></i>
                                                <p>Last 21days clients</p>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('resource_All-Applicants')
                                        <li class="nav-item">
                                            <a href="{{ route('last2months') }}"
                                               class="pl-5 nav-link {{ request()->is('last2months/active') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-users"></i>
                                                <p>All clients</p>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('last2monthsBlockedApplicants') }}"
                                   class="pl-5 nav-link {{ request()->is('sales/active') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-lock"></i>
                                    <p>Blocked Clients</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('TempNotInterestedApplicants') }}"
                                   class="pl-5 nav-link {{ request()->is('sales/active') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-times"></i>
                                    <p>Not Interested</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('NoResponseApplicants') }}"
                                   class="pl-5 nav-link {{ request()->is('sales/active') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-clock"></i>
                                    <p>No Response Clients</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('potential-call-back-clients') }}"
                                   class="pl-5 nav-link {{ request()->is('potential-call-back-clients/active') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-clock"></i>
                                    <p>Callback client</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                @endcanany

                @canany(['CRM_Sent-CVs_list','CRM_Sent-CVs_request','CRM_Sent-CVs_save','CRM_Sent-CVs_reject','CRM_Rejected-CV_list','CRM_Request_list','CRM_Rejected-By-Request_list','CRM_Confirmation_list','CRM_Rebook_list','CRM_Attended_list','CRM_Declined_list','CRM_Not-Attended_list','CRM_Start-Date_list','CRM_Start-Date-Hold_list','CRM_Invoice_list','CRM_Dispute_list','CRM_Paid_list'])

                    <li class="nav-item {{ request()->is('users/*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('users') ? 'menu-open' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                CRM
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            @can('CRM_Sent-CVs_list')
                                <li class="nav-item">
                                    <a href="{{ route('sent_cv') }}"
                                       class="pl-5 nav-link {{ request()->is('sales/active') ? 'active' : '' }}">
                                        <i class="far fa-envelope"></i>
                                        <p>Sent CVs</p>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a href="{{ route('qualified_staff_cv') }}"
                                   class="pl-5 nav-link {{ request()->is('qualified_staff_cv/active') ? 'active' : '' }}">
                                    <i class="fas fa-user-check"></i>
                                    <p>Sent CVs (Qualified Staff)</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('non-qualified_staff_cv') }}"
                                   class="pl-5 nav-link {{ request()->is('qualified_staff_cv/active') ? 'active' : '' }}">
                                    <i class="fas fa-user-times"></i>
                                    <p>Sent CVs (Non Qualified Staff)</p>
                                </a>
                            </li>

                            @can('CRM_Rejected-CV_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-reject-cvs') }}"
                                       class="pl-5 nav-link {{ request()->is('qualified_staff_cv/active') ? 'active' : '' }}">
                                        <i class="fas fa-times-circle"></i>
                                        <p>Reject CV</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Request_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-request_cv') }}"
                                       class="pl-5 nav-link {{ request()->is('crm-request_cv/active') ? 'active' : '' }}">
                                        <i class="fas fa-hand-holding-medical"></i>
                                        <p>Request (Qualified Staff)</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Request_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-request_non-qualified-cv') }}"
                                       class="pl-5 nav-link {{ request()->is('crm-request_cv/active') ? 'active' : '' }}">
                                        <i class="fas fa-hands-helping"></i>
                                        <p>Request (Non Qualified Staff)</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Rejected-By-Request_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-reject-request') }}"
                                       class="pl-5 nav-link {{ request()->is('crm-reject-request/active') ? 'active' : '' }}">
                                        <i class="fas fa-ban"></i>
                                        <p>Request Reject CV</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Confirmation_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-confirmation_cv') }}"
                                       class="pl-5 nav-link {{ request()->is('crm-reject-request/active') ? 'active' : '' }}">
                                        <i class="fas fa-check-circle"></i>
                                        <p>Confirmation CVs</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Rebook_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-rebook') }}"
                                       class="pl-5 nav-link {{ request()->is('crmRebook/active') ? 'active' : '' }}">
                                        <i class="fas fa-calendar-plus"></i>
                                        <p>Rebook</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Attended_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-pre-start') }}"
                                       class="pl-5 nav-link {{ request()->is('crm-pre-start/active') ? 'active' : '' }}">
                                        <i class="fas fa-user-check"></i>
                                        <p>Attended Pre Start</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Declined_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-declined-cvs') }}"
                                       class="pl-5 nav-link {{ request()->is('crm-pre-start/active') ? 'active' : '' }}">
                                        <i class="fas fa-user-times"></i>
                                        <p>CRM Declined</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Not-Attended_list')
                                <li class="nav-item">
                                    <a href="{{ route('crmNotAttendedCvs') }}"
                                       class="pl-5 nav-link {{ request()->is('crmNotAttendedCvs/active') ? 'active' : '' }}">
                                        <i class="fas fa-user-slash"></i>
                                        <p>CRM Not Attended</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Start-Date_list')
                                <li class="nav-item">
                                    <a href="{{ route('crm-start-date') }}"
                                       class="pl-5 nav-link {{ request()->is('crm-start-date/active') ? 'active' : '' }}">
                                        <i class="far fa-calendar-alt"></i>
                                        <p>CRM Start</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Start-Date-Hold_list')
                                <li class="nav-item">
                                    <a href="{{ route('crmStartDateHoldCvs') }}"
                                       class="pl-5 nav-link {{ request()->is('crmStartDateHoldCvs/active') ? 'active' : '' }}">
                                        <i class="nav-icon far fa-calendar-times"></i>
                                        <p>CRM Start Hold</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Invoice_list')
                                <li class="nav-item">
                                    <a href="{{ route('crmInvoiceCvs') }}"
                                       class="pl-5 nav-link {{ request()->is('crmInvoiceCvs/active') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                        <p>CRM Invoice</p>
                                    </a>
                                </li>
                                    <li class="nav-item">
                                    <a href="{{ route('crmInvoiceSentCvs') }}"
                                       class="pl-5 nav-link {{ request()->is('crmInvoiceSentCvs/active') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                        <p>CRM Sent Invoice</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Dispute_list')
                                <li class="nav-item">
                                    <a href="{{ route('crmDisputeCvs') }}"
                                       class="pl-5 nav-link {{ request()->is('crmDisputeCvs/active') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-exclamation-circle"></i>
                                        <p>CRM Dispute CV</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CRM_Paid_list')
                                <li class="nav-item">
                                    <a href="{{ route('crmPaidCvs') }}"
                                       class="pl-5 nav-link {{ request()->is('crmPaidCvs/active') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                                        <p>CRM Paid CV</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>

                @endcanany
                    @can('postcode-finder_search')

                    <li class="nav-item">
                        <a href="{{ route('postcodeFinder') }}"
                           class="nav-link {{ request()->is('roles/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-search"></i>
                            <p>
                                Job Finder
                            </p>
                        </a>
                    </li>
                @endcanany
                <li class="nav-item">
                    <a href="{{ route('special_lists.index') }}"
                       class="nav-link {{ request()->is('special_lists/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>
                            Specialist Title
                        </p>
                    </a>
                </li>

                @canany(['role_list','role_create','role_view','role_edit','role_delete','role_assign-role'])
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}"
                           class="nav-link {{ request()->is('roles/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-lock"></i>
                            <p>
                                Roles & Permissions
                            </p>
                        </a>
                    </li>
                @endcanany

                {{--                @endrole--}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
