@extends('layouts.master')
@section('title', 'Home')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" />--}}
{{--    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">--}}
{{--    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">--}}
    <link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <style>
        .divider-line {
            text-align: center;
        }

        .divider-line hr {
            border-top: 2px solid #3490dc; /* Change color here */
            width: 50%;
            margin: 0 auto;
        }
        /* Styles for the loader icon */
        .loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .dot {
            width: 50px;
            height: 50px;
            background-color: #3498db; /* Change color as needed */
            border-radius: 50%;
            display: inline-block;
            animation: bounce 1.5s infinite ease-in-out;
        }

        .dot.dot1 {
            animation-delay: 0s;
            background-color: #e74c3c; /* Change color as needed */
        }

        .dot.dot2 {
            animation-delay: 0.3s;
            background-color: #2ecc71; /* Change color as needed */
        }

        .dot.dot3 {
            animation-delay: 0.6s;
            background-color: #f1c40f; /* Change color as needed */
        }

        .dot.dot4 {
            animation-delay: 0.9s;
            background-color: #9b59b6; /* Change color as needed */
        }

        @keyframes bounce {
            0%, 100% {
                transform: scale(0);
            }
            50% {
                transform: scale(1);
            }
        }


    </style>
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>

@endsection
@section('content')


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-lg-3 col-md-6 pr-lg-1 totalUser">
                        <div class="card infocard shadow-none">
                            <div class="card-body border-radius-top p-0 " style=" cursor: pointer;">
                                <div class="row m-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2" style="width: 100%">
                                        <li class="item p-0 pt-2 pb-2">
                                            <div class="infoBox product-img">
                                                <img class="m-4" src="{{ asset('svg/user.svg') }}"
                                                    style="width: 34px !important;height:34px !important">
                                            </div>
                                            <div class="product-info pt-2 pl-4">
                                                <p class="product-title pl-2 mb-0 infoTitle">
                                                    Total Team Members
                                                </p>
                                                <p class="product-description pl-2 mb-0 infoDescription">
                                                    {{ $userCount }}
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 pl-lg-1 pr-lg-1 seasonSale">
                        <div class="card infocard shadow-none">
                            <div
                            class="card-body border-radius-top p-0 " style=" cursor: pointer;">
                                <div class="row m-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2" style="width: 100%">
                                        <li class="item p-0 pt-2 pb-2">
                                            <div class="infoBox product-img">
                                                <img class="m-4" src="{{ asset('svg/sales.svg') }}"
                                                    style="width: 34px !important;height:34px !important">
                                            </div>
                                            <div class="product-info pt-2 pl-4">
                                                <p class="product-title pl-2 mb-0 infoTitle">
                                                    Total Sales
                                                </p>
                                                <p class="product-description pl-2 mb-0 infoDescription">
                                                    {{ $sales }}
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 pl-lg-1 pr-lg-1 paidPlayer">
                        <div class="card infocard shadow-none">
                            <div class="card-body border-radius-top p-0 " style=" cursor: pointer;">
                                <div class="row m-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2" style="width: 100%">
                                        <li class="item p-0 pt-2 pb-2">
                                            <div class="infoBox product-img">
                                                <img class="m-4" src="{{ asset('svg/hand.svg') }}"
                                                    style="width: 34px !important;height:34px !important">
                                            </div>
                                            <div class="product-info pt-2 pl-4">
                                                <p class="product-title pl-2 mb-0 infoTitle">
                                                    Total Client Members
                                                </p>
                                                <p class="product-description infoDescription pl-2 mb-0">
                                                    {{ $clients }}
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 pl-lg-1 supportRequest">
                        <div class="card infocard shadow-none">
                            <div class="card-body border-radius-top p-0 " style=" cursor: pointer;">
                                <div class="row m-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2" style="width: 100%">
                                        <li class="item p-0 pt-2 pb-2">
                                            <div class="infoBox product-img">
                                                <img class="m-4 filter-white" src="{{ asset('svg/sales.svg') }}"
                                                     style="width: 34px !important;height:34px !important">
                                            </div>
                                            <div class="product-info pt-2 pl-4">
                                                <p class="product-title pl-2 mb-0 infoTitle">
                                                    Pending Sales
                                                </p>
                                                <p class="product-description pl-2 mb-0 infoDescription">
                                                    {{ $pendingSales }}
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>&nbsp;

                </div>
{{--                <div class="row mb-2">--}}
{{--                    <div class="col-lg-3 col-md-6 pl-lg-1 supportRequest">--}}
{{--                        <div class="card infocard shadow-none">--}}
{{--                            <div class="card-body border-radius-top p-0 " style=" cursor: pointer;">--}}
{{--                                <div class="row m-0">--}}
{{--                                    <ul class="products-list product-list-in-card pl-2 pr-2" style="width: 100%">--}}
{{--                                        <li class="item p-0 pt-2 pb-2">--}}
{{--                                            <div class="infoBox product-img">--}}
{{--                                                <img class="m-4 filter-white" src="{{ asset('svg/sales.svg') }}"--}}
{{--                                                    style="width: 34px !important;height:34px !important">--}}
{{--                                            </div>--}}
{{--                                            <div class="product-info pt-2 pl-4">--}}
{{--                                                <p class="product-title pl-2 mb-0 infoTitle">--}}
{{--                                                    Pending Sales--}}
{{--                                                </p>--}}
{{--                                                <p class="product-description pl-2 mb-0 infoDescription">--}}
{{--                                                    {{ $pendingSales }}--}}
{{--                                                </p>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--}}
{{--                    <div class="col-lg-3 col-md-6 pl-lg-1 supportRequest">--}}
{{--                        <div class="card infocard shadow-none">--}}
{{--                            <div class="card-body border-radius-top p-0 " style=" cursor: pointer;">--}}
{{--                                <div class="row m-0">--}}
{{--                                    <ul class="products-list product-list-in-card pl-2 pr-2" style="width: 100%">--}}
{{--                                        <li class="item p-0 pt-2 pb-2">--}}
{{--                                            <div class="infoBox product-img">--}}
{{--                                                <img class="m-4 filter-white" src="{{ asset('svg/sales.svg') }}"--}}
{{--                                                    style="width: 34px !important;height:34px !important">--}}
{{--                                            </div>--}}
{{--                                            <div class="product-info pt-2 pl-4">--}}
{{--                                                <p class="product-title pl-2 mb-0 infoTitle">--}}
{{--                                                    Pending Sales--}}
{{--                                                </p>--}}
{{--                                                <p class="product-description pl-2 mb-0 infoDescription">--}}
{{--                                                    {{ $pendingSales }}--}}
{{--                                                </p>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div><!-- /.container-fluid -->
        </section>


        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">

                <div class="row">
                    <!-- Left col -->
                    <div class="col-md-8">
                        <!-- MAP & BOX PANE -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">US-Visitors Report</h3>
{{--                                <div>--}}
{{--                                    <input type="date" name="" class=""><br>--}}
{{--                                </div>--}}
                                <div class="card-tools">
                                    <input type="hidden" id="start_date" name="start_date">
                                    <input type="hidden" id="end_date" name="end_date">
                                    <button type="submit" class="btn btn-primary btn-sm daterange" title="Date range">
                                        <i class="far fa-calendar-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>

                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="card bg-light rounded-0">


                                    <!-- Applicants -->
                                    <div class="card-header bg-gradient-primary text-light">
                                        <h5 class="card-title">Clients</h5>
                                    </div>
                                    <div class="card-body d-md-flex justify-content-between flex-wrap client_show">
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <a href="#" class="qualified_model_form" data-user_home="nurses">
                                                <i class="fas fa-user-nurse text-purple" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="no_of_nurses">{{$clientQualified}}</h6>
                                                <span class="text-muted">Qualified</span></a>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <a href="#" class="qualified_model_form" data-user_home="non-nurses">
                                                <i class="fas fa-user text-secondary" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="no_of_non_nurses">{{$clientNonQualified}}</h6>
                                                <span class="text-muted">Non Qualified</span>
                                                </a>
                                            </div>
                                        </div>
                                        @include('partialPages.inc.model_form')

                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <i class="fas fa-phone text-primary" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="no_of_callbacks">{{$clientCallback}}</h6>
                                                <span class="text-muted">Not job</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <i class="fas fa-user-times text-danger" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="no_of_not_interested">{{$clientBlock}}</h6>
                                                <span class="text-muted">Block Client</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /applicants -->

                                    <!-- Sales -->
                                    <div class="card-header bg-gradient-purple text-light">
                                        <h5 class="card-title">Sales</h5>
                                    </div>
                                    <div class="card-body d-md-flex justify-content-between flex-wrap">
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <a href="#" id="sale_open_link" >
                                                   <i class="fas fa-door-open text-success" style="font-size: 30px;"></i>
                                                    <h6 class="font-weight-semibold mt-2" id="daily_open_sales">{{$saleOpen}}</h6>
                                                    <span class="text-muted">Open</span>
                                                </a>
                                                <form id="open_sale_form" action="/sale_open" method="POST">
                                                    @csrf
                                                    <input type="hidden" class="sanat" id="start_date_sale" name="start_date" value="2024-04-24">
                                                    <input type="hidden" class="sanat" id="end_date_sale" name="end_date" value="2024-04-24">
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <a href="#" id="sale_close_link">
                                                    <i class="fas fa-door-closed text-danger" style="font-size: 30px;"></i>
                                                    <h6 class="font-weight-semibold mt-2" id="daily_close_sales">{{$closeSalesToday}}</h6>
                                                    <span class="text-muted">Close</span>
                                                </a>
                                                <form id="close_sale_form" action="/sale_close" method="POST">
                                                    @csrf
                                                    <input type="hidden" class="sanat" id="close_start_date_sale" name="close_start_date" value="2024-04-24">
                                                    <input type="hidden" class="sanat" id="close_end_date_sale" name="close_end_date" value="2024-04-24">
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <i class="fas fa-building text-primary" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="daily_psl_offices">{{$holdSalesToday}}</h6>
                                                <span class="text-muted">Sale Hold</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <i class="fas fa-tasks text-info" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="daily_non_psl_offices">{{$pendingSalesToday}}</h6>
                                                <span class="text-muted">Sale Pending</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- /sales -->

                                    <!-- Quality -->
                                    <div class="card-header bg-gradient-warning text-light">
                                        <h5 class="card-title">Quality</h5>
                                    </div>
                                    <div class="card-body d-md-flex justify-content-between flex-wrap">
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <i class="fas fa-file-medical text-info" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="daily_cvs">{{$quality['daily_cvs']}}</h6>
                                                <span class="text-muted">CVs (Sent)</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <i class="fas fa-stop text-danger" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="daily_cvs_rejected">{{$quality['daily_cvs_rejected']}}</h6>
                                                <span class="text-muted">CVs Rejected</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="text-center">
                                                <i class="fas fa-clinic-medical text-success" style="font-size: 30px;"></i>
                                                <h6 class="font-weight-semibold mt-2" id="daily_cvs_cleared">{{$quality['daily_cvs_rejected']}}</h6>
                                                <span class="text-muted">CVs Cleared</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /quality -->

                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>


                        <!-- TABLE: LATEST ORDERS -->
                        <div class="card">
                            <div class="card-header border-transparent" style="background-color:#e1d7d7">
                                <h3 class="card-title">Latest Teams Member</h3>

{{--                                <h3 class="card-title">Latest Teams Member</h3>--}}

                                <div class="d-flex align-items-center" style="float: right;">
                                    <label for="from-date" style="margin-right: 5px; font-weight: bold; color: #333;">From:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control form-control-sm" id="user_stats_start_date_value" style="width: 50%; border-color: #007bff;">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                    <label for="to-date" style="margin-left: 10px; font-weight: bold; color: #333;">To:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control form-control-sm" id="user_stats_end_date_value" style="width: 50%; border-color: #007bff;">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-light border mr-2" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>


                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table   table-borderless font12 " id="dashboard-table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>status</th>
                                            <th>role</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.card-body -->

                            <!-- /.card-footer -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-4">
                        <!-- Info Boxes Style 2 -->
                        <div class="info-box mb-3 bg-gradient-info">
                            <!-- Left side: Clear CV -->
                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Clear CV</span>
                                    <span class="info-box-number" id="cvs_count">{{$crm_data['crm_sent']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.col -->

                            <!-- Middle side: Qualified -->
                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Request CV</span>
                                    <span class="info-box-number" id="request_count">{{$crm_data['crm_requested']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>

                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Confirmation</span>
                                    <span class="info-box-number" id="confirmation_count">{{$crm_data['crm_confirmed']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.info-box -->

                        <!-- /.info-box -->
                        <div class="info-box mb-3 bg-gradient-danger">

                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="fas fa-ban"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Reject CV</span>
                                    <span class="info-box-number" id="rejected_count">{{$crm_data['crm_rejected']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>

                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Request Rejection</span>
                                    <span class="info-box-number" id="rejected_requested_count">{{$crm_data['crm_request_rejected']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>

                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Decline</span>
                                    <span class="info-box-number" id="declined_count">{{$crm_data['crm_declined']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box-content -->
                        </div>

                        {{--                        <!-- /.info-box -->rebook,attend not attend --}}
                        <div class="info-box mb-3 bg-gradient-warning">

                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="fas fa-bookmark"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Rebook</span>
                                    <span class="info-box-number" id="rebook_count">{{$crm_data['crm_rebook']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>

                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="far fa-calendar-check"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Attend</span>
                                    <span class="info-box-number" id="prestart_count">{{$crm_data['crm_prestart_attended']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>

                            <div class="col-sm-4">
                                <span class="info-box-icon"><i class="far fa-calendar-times"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Not Attend</span>
                                    <span class="info-box-number" id="attended_count">{{$crm_data['crm_not_attended']}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <div class="info-box mb-3 bg-gradient-purple">
                            <div class="col-sm-4">
                             <span class="info-box-icon">
                                <i class="fas fa-flag"></i> </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Start</span>
                                    <span class="info-box-number" id="started_count">{{$crm_data['crm_date_started']}}</span>
                                </div>
                            </div>

                            <div class="col-sm-4">
    <span class="info-box-icon">
      <i class="fas fa-clock"></i> </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Start hold</span>
                                    <span class="info-box-number" id="start_hold_count">{{$crm_data['crm_start_date_held']}}</span>
                                </div>
                            </div>

                            <div class="col-sm-4">
    <span class="info-box-icon">
      <i class="fas fa-file-invoice"></i> </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Invoice</span>
                                    <span class="info-box-number" id="invoice_count">{{$crm_data['crm_invoiced']}}</span>
                                </div>
                            </div>

                        </div>

                        <div class="info-box mb-3 bg-gradient-dark">
                            <div class="col-sm-4">
                            <span class="info-box-icon">
                              <i class="fas fa-exclamation-triangle"></i> </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Dispute</span>
                                    <span class="info-box-number" id="disputed_count">{{$crm_data['crm_disputed']}}</span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                            <span class="info-box-icon">
                            <i class="fas fa-check-circle"></i> </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Paid</span>
                                    <span class="info-box-number" id="paid_count">{{$crm_data['crm_paid']}}</span>
                                </div>
                            </div>

                        </div>


                    </div>
                    <!-- /.col -->
                </div>


            </div>

        </section>
        <!-- /.content -->
    </div>

    <div class="modal fade" id="userStatisticsModal" tabindex="-1" role="dialog" aria-labelledby="userStatisticsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="userStatisticsModalLabel">User Statistics</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="user_stats_details">
                    <!-- User statistics details will be displayed here -->
                </div>
                <div class="modal-footer">
                    <!-- Any modal footer content goes here -->
                </div>
            </div>
        </div>
    </div>

    <div class="loader" style="display: none;">
        <div class="dot dot1"></div>
        <div class="dot dot2"></div>
        <div class="dot dot3"></div>
        <div class="dot dot4"></div>
    </div>


    {{--    <div id="calendar" style=""></div>--}}
    <!-- /.content-wrapper -->
@endsection


@section('script')
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>--}}
{{--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
<script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js')}}"></script>
<!-- date-range-picker -->
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>


<script>
    // $('#loader').hide();

        // JavaScript code to show the client section based on a condition
        // Example: Show the client section when the page is loaded
        $(document).ready(function() {
            $('.daterange').daterangepicker({
                opens: 'left' // Adjust this according to your preference
            }, function(start, end, label) {
                // Update hidden input fields with selected start and end dates
                $('#start_date').val(start.format('YYYY-MM-DD'));

                $('#start_date_sale').val(start.format('YYYY-MM-DD'));
                $('#end_date_sale').val(end.format('YYYY-MM-DD'));
                $('#close_start_date_sale').val(start.format('YYYY-MM-DD'));
                $('#close_end_date_sale').val(end.format('YYYY-MM-DD'));
                $('#end_date').val(end.format('YYYY-MM-DD'));
                $('.loader').show();
                console.log('sanat');
                $('body').addClass('blurred');
                // Make AJAX call to fetch data based on selected date range
                fetchData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
                cvshData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));

           });
        });
    document.getElementById('sale_open_link').addEventListener('click', function() {
        document.getElementById('open_sale_form').submit();
    });
    document.getElementById('sale_close_link').addEventListener('click', function() {
        document.getElementById('close_sale_form').submit();
    });
        function fetchData(startDate, endDate) {


            $.ajax({
                url: '/fetch-data',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    setTimeout(function() {
                        // Hide loader after delay (replace this with your actual functionality)
                        $('.loader').hide();
                        $('body').removeClass('blurred');
                    }, 2000);
                    // console.log(response);
                    var data_stat = response.data;

                    // Update HTML elements with the fetched data
                    $('#no_of_nurses').text(data_stat.clientQualified);
                    $('#no_of_non_nurses').text(data_stat.clientNonQualified);
                    $('#no_of_callbacks').text(data_stat.clientCallback);
                    $('#no_of_not_interested').text(data_stat.clientBlock);
                    $('#daily_open_sales').text(data_stat.saleOpen);
                    $('#daily_close_sales').text(data_stat.closeSalesToday);
                    $('#daily_psl_offices').text(data_stat.holdSalesToday);
                    $('#daily_non_psl_offices').text(data_stat.pendingSalesToday);
                    $('#daily_cvs').text(data_stat.quality.daily_cvs);
                    $('#daily_cvs_rejected').text(data_stat.quality.daily_cvs_rejected);
                    $('#daily_cvs_cleared').text(data_stat.quality.daily_cvs_cleared);
                },
                error: function(xhr, status, error) {
                    setTimeout(function() {
                        // Hide loader after delay (replace this with your actual functionality)
                        $('.loader').hide();
                    }, 2000);
                    // Handle error
                }
            });
        }
        function cvshData(startDate, endDate) {
            setTimeout(function() {
                // Hide loader after delay (replace this with your actual functionality)
                $('.loader').hide();
            }, 2000);

            $.ajax({
                url: '/fetch-cvs-data',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    var crm = response.crm_data;
                    console.log(crm);

                    // Update the info-boxes with the fetched data
                    $('#cvs_count').text(crm.crm_sent);
                    $('#request_count').text(crm.crm_requested);
                    $('#confirmation_count').text(crm.crm_confirmed);
                    $('#rejected_count').text(crm.crm_rejected);
                    $('#rejected_requested_count').text(crm.crm_request_rejected);
                    $('#declined_count').text(crm.crm_declined);
                    $('#rebook_count').text(crm.crm_rebook);
                    $('#prestart_count').text(crm.crm_prestart_attended);
                    $('#attended_count').text(crm.crm_not_attended);
                    $('#started_count').text(crm.crm_date_started);
                    $('#start_hold_count').text(crm.crm_start_date_held);
                    $('#invoice_count').text(crm.crm_invoiced);
                    $('#disputed_count').text(crm.crm_disputed);
                    $('#paid_count').text(crm.crm_paid);
                },

                error: function(xhr, status, error) {
                    // Handle error
                }
            });
        }
        $('.qualified_model_form').on('click', function(event) {
            // Prevent the default behavior of the anchor tag
            event.preventDefault();

            // Retrieve start and end dates
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var user_home = $(this).data('user_home');
            $('#exampleModal').modal('show');
            // Open modal and load data based on start and end dates
            openModalWithData(startDate, endDate,user_home);
        });
        function openModalWithData(startDate, endDate,user_home) {
            $.ajax({
                url: "{{ route('applicant_home_details_stats') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_key: 'test',
                    start_date: startDate,
                    end_date:endDate,
                    user_home:user_home,
                    // date_value_end:date_value_end
                },
                success: function(response) {
                    // Log the entire response for further inspection
                    var jobCategory=response.category;

                    var counts = response.counts; // Accessing the 'counts' object from the response
                    // Calculate total record count
                    var totalRecordCount = 0;
                    for (var source in counts) {
                        if (counts.hasOwnProperty(source)) {
                            totalRecordCount += counts[source];
                        }
                    }
                    // Update the total record count in the modal title
                    $('#totalRecordCount').text('(' + totalRecordCount + ' total record)');

                    var html = '<div class="row">';
                    // Iterate through the counts object and generate grid system for each source
                    for (var source in counts) {
                        if (counts.hasOwnProperty(source) && source !== '') {
                            html += '<div class="col-md-3 mb-3">';
                            html += '<div class="card">';
                            html += '<div class="card-body">';
                            html += '<h5 class="card-title">' + source + '</h5>';
                            html += '<p class="card-text">Count: ' + counts[source] + '</p>';
                            // Add a button to make a new route call when clicked
                            // html += '<button class="btn btn-primary" onclick="makeRouteCall(\'' + source + '\')">View Details</button>';
                            // html += '</div></div></div>';
                            html += '<button class="btn btn-primary" onclick="makeRouteCall(\'' + source +'\', \'' + startDate + '\', \'' + endDate + '\', \'' + jobCategory + '\')">View Details</button>';
                            html += '</div></div></div>';
                        }
                    }
                    // Add design for NoSource and count display
                    if (counts[''] === undefined || counts[''] === null) {
                        // If counts[''] is undefined or null, set count to 0
                        var count = 0;
                    } else {
                        // Otherwise, assign the value of counts[''] to count
                        var count = counts[''];
                    }
                    html += '<div class="col-md-3 mb-3">';
                    html += '<div class="card">';
                    html += '<div class="card-body">';
                    html += '<h5 class="card-title">NoSource</h5>';
                    html += '<p class="card-text">Count: ' + count + '</p>';
                    // Add a button to make a new route call when clicked
                    // html += '<button class="btn btn-primary" onclick="makeRouteCall(\'NoSource\')">View Details</button>';
                    html += '<button class="btn btn-primary" onclick="makeRouteCall(\'NoSource\', \'' + startDate + '\', \'' + endDate + '\', \'' + jobCategory + '\')">View Details</button>';

                    html += '</div></div></div>';
                    html += '</div>';

                    // Append the generated HTML to the designated element
                    $('#applicant_stats_details').html(html);
                },


// Function to make a new route call

            error: function(response){
                    let raw_html = '<p>WHOOPS! Something Went Wrong!!</p>';
                    $('#applicant_deail_stats_details').html(raw_html);

                }
            });


        }
    function makeRouteCall(source, startDate, endDate, jobCategory) {
        // var route = 'client-cvs-data/' + source + '/' + startDate + '/' + endDate + '/' + jobCategory;
        // window.location.href = route;
        var route = 'client-cvs-data/' + source + '/' + startDate + '/' + endDate + '/' + jobCategory;

        // Open the URL in a new tab
        window.open(route, '_blank');

        // Navigate to another URL in the current tab
        // var anotherUrl = 'another-url';
        // window.location.href = anotherUrl;

    }


        $(document).ready(function() {

            $('#dashboard-table').DataTable({
                // "ajax": {
                //     "url": "getUsersDashboard",
                //     "dataSrc": "" // If your data is not wrapped in a specific property
                // },
                ajax: "{{ url('getUsersDashboard') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    // {data: 'date', name: 'date'},
                    // {data: 'time', name: 'time'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phoneNumber', name: 'phoneNumber'},
                    {data: 'role', name: 'role'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'},
                ]
            });
{{--            $('#dashboard-table').on('click', '.user-statistics', function() {--}}
{{--                // var userId = $(this).data('user_key');--}}
{{--                // alert(userId);--}}
{{--                // var fullName = $(this).data('user_name');--}}

{{--                var user_key = $(this).data('user_key');--}}
{{--                var user_name = $(this).data('user_name');--}}
{{--                var start_date = $('#user_stats_start_date_value').val();--}}
{{--                var end_date = $('#user_stats_end_date_value').val();--}}
{{--                // console.log(start_date+' '+end_date);--}}
{{--// console.log(user_key+' '+start_date+' '+end_date);--}}
{{--                $('#user_name').html(user_name);--}}

{{--                $.ajax({--}}
{{--                    url: "{{ route('userStatistics') }}",--}}
{{--                    type: "GET",--}}
{{--                    data: {--}}
{{--                        _token: "{{ csrf_token() }}",--}}
{{--                        user_key: user_key,--}}
{{--                        user_name: user_name,--}}
{{--                        start_date: start_date,--}}
{{--                        end_date: end_date--}}
{{--                    },--}}
{{--                    success: function(response){--}}
{{--                        $('#user_stats_details').html(response);--}}
{{--                        $('#user_s_date').html(start_date);--}}
{{--                        $('#user_e_date').html(end_date);--}}
{{--                    },--}}
{{--                    error: function(response){--}}
{{--                        let raw_html = '<p>WHOOPS! Something Went Wrong!!</p>';--}}
{{--                        $('#user_stats_details').html(raw_html);--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
        });
        $(document).on('click', '.user-statistics', function (event) {
            console.log("Button clicked");
            var user_key = $(this).data('user_key');
            var user_name = $(this).data('user_name');
            var start_date = $('#user_stats_start_date_value').val();
            var end_date = $('#user_stats_end_date_value').val();
            // console.log(start_date+' '+end_date);
// console.log(user_key+' '+start_date+' '+end_date);
            $('#user_name').html(user_name);

            $.ajax({
                url: "{{ route('userStatistics') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_key: user_key,
                    user_name: user_name,
                    start_date: start_date,
                    end_date: end_date
                },
                success: function(response){
                    $('#user_stats_details').html(response);
                    $('#user_s_date').html(start_date);
                    $('#user_e_date').html(end_date);
                    $('#userStatisticsModal').modal('show'); // Show the modal after successful response
                },
                error: function(response){
                    let raw_html = '<p>WHOOPS! Something Went Wrong!!</p>';
                    $('#user_stats_details').html(raw_html);
                }
            });
        });


    </script>
{{--    <script src="{{ asset('js/home.js') }}"></script>--}}

@endsection
