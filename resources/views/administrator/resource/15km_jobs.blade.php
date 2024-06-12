@extends('layouts.master')

@section('style')



@endsection
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">
        <!-- Page header -->
        {{--        <div class="page-header page-header-dark has-cover" style="border: 1px solid #ddd; border-bottom: 0;">--}}

        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Client Details</h5>
                <input type="hidden" id="hidden_applicant_id" value="{{ $applicant->id }}">
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card border-left-3 border-left-primary rounded-left-0" style="background-color: #f0f0f0;">
                        <div class="card-body">
                            <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                <div>
                                    <h5>Title: <span class="font-weight-bold">{{ $applicant->app_job_title }}</span></h5>
                                    <ul class="list list-unstyled mb-0">
                                        <li>Name: <span class="font-weight-bold">{{ $applicant->app_name }}</span></li>
                                        <li>Postcode: <span class="font-weight-bold">{{ $applicant->app_postcode }}</span></li>
                                        <li>Category: <span class="font-weight-bold">{{ $applicant->app_job_category }}</span></li>
                                    </ul>
                                </div>

                                <div class="text-sm-right mb-0 mt-3 mt-sm-0 ml-auto">
                                    <h5>Phone #: <span class="font-weight-bold">{{ $applicant->app_phone }}</span></h5>
                                    <ul class="list list-unstyled mb-0">
                                        <li>Landline: <span class="font-weight-bold">{{ $applicant->app_phoneHome }}</span></li>
                                        <li class="dropdown">
                                            Status: &nbsp;
                                            <a href="#" class="badge bg-success align-top">{{ $applicant->app_status }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-sm-flex justify-content-sm-between align-items-sm-center">
                <span>
                    <span class="font-weight-semibold"></span>
                </span>

                            <ul class="list-inline list-inline-condensed mb-0 mt-2 mt-sm-0">
                                <li class="list-inline-item">
                                    Created On: <span class="font-weight-bold">{{ $applicant->created_at }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Default ordering -->
            @if (\Session::has('notFoundCv'))
                <div class="alert alert-danger alert-dismissible" style="border-left: 3px solid; border-top: 0; border-right: 0; border-bottom: 0;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {!! \Session::get('notFoundCv') !!}
                </div>
            @endif
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Active Client's Jobs Within 15KM</h5>
            </div>
            <div class="card">

                <div class="card-body">
                    {{--With DataTables you can alter the ordering characteristics of the table at initialisation time. Using the <code>order</code> initialisation parameter, you can set the table to display the data in exactly the order that you want. The <code>order</code> parameter is an array of arrays where the first value of the inner array is the column to order on, and the second is <code>'asc'</code> or <code>'desc'</code> as required. The table below is ordered (descending) by the <code>DOB</code> column.--}}
                </div>

                <table class="table table-hover table-striped" id="jobs_within_15km_sample">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Office</th>
                        <th>Unit</th>
                        <th>Job Title</th>
                        <th>Job Type</th>
                        <th>Job Postcode</th>
                        <th>Job Timing</th>
                        <th>Salary</th>
                        <th>Experience</th>
                        <th>Qualification</th>
                        <th>Status</th>
                        <th>Cv's Limit</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!-- /default ordering -->

        </div>
        <!-- /content area -->

        @endsection

        @section('script')
            <script>

                $(document).ready(function() {
                    $.fn.dataTable.ext.errMode = 'none';
                    var applicant = $("#hidden_applicant_id").val();

                    $('#jobs_within_15km_sample').DataTable({
                        "Processing": true,
                        "ServerSide": true,
                        "ajax":"{!! url('get15kmJobsAvailableAjax') !!}/"+applicant,
                        "order": [],
                        "columns": [
                            { "data":"updated_at" },
                            { "data":"sale_added_time", "orderable": false },
                            { "data":"head_office" },
                            { "data":"head_office_unit" },
                            { "data":"job_title" },
                            { "data":"job_type", "orderable": true },
                            { "data":"postcode" },
                            { "data":"time" },
                            { "data":"salary" },
                            { "data":"experience" },
                            {"data":"qualification"},
                            {"data":"status"},
                            {"data":"cv_limit", "orderable": false},
                            {"data":"action", "orderable": false}
                        ],
                        "rowCallback": function( row, data ) {
                            var dateCell = data.updated_at;
                            var sortedDate = dateSorting (dateCell);
                            $('td:eq(0)', row).html(sortedDate);
                        }
                        /***
                         ,
                         "columnDefs": [
                         {
                        "width": "10%",
                        "targets": 0
                    }
                         ],
                         "drawCallback": function (response) {
                    console.log('iDrawError ', response.iDrawError);
                 }
                         */
                    });

                });

            </script>
            <script>

                function dateSorting(date_timestamp) {

                    var a = new Date(date_timestamp * 1000);
                    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                    var days = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th', '21st', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th', '30th', '31st'];
                    var year = a.getFullYear();
                    var month = months[a.getMonth()];
                    var date = days[a.getDate()-1];
                    var date_time = date + ' ' + month + ' ' + year;

                    return date_time;
                }

            </script>
@endsection
