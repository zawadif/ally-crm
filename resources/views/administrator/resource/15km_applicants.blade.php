@extends('layouts.master')
{{--<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">--}}
{{--<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">--}}
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">

<style>
    /* CSS styling */
    .dataTables_filter {
        /* Style the container of the search input */
        margin-bottom: 10px; /* Optional: Adjust margin as needed */
    }

    .dataTables_filter label {
        /* Style the label */
        font-weight: normal; /* Optional: Adjust font weight as needed */
    }

    .dataTables_filter input.form-control {
        /* Style the search input */
        padding: 6px 12px; /* Optional: Adjust padding as needed */
        font-size: 14px; /* Optional: Adjust font size as needed */
        border-radius: 4px; /* Optional: Adjust border radius as needed */
        border: 1px solid #ccc; /* Optional: Adjust border color as needed */
    }

    .dataTables_filter input.form-control:focus {
        /* Style the search input on focus */
        border-color: #66afe9; /* Optional: Adjust focus border color as needed */
        outline: 0; /* Optional: Remove outline */
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, 0.6); /* Optional: Add box shadow */
    }

</style>
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        {{--        <div class="page-header page-header-dark has-cover" style="border: 1px solid #ddd; border-bottom: 0;">--}}

        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
{{--            <div class="card-header header-elements-inline">--}}
{{--                <h5 class="card-title">Job Details</h5>--}}
{{--                <input type="hidden" id="hidden_job_value" value="{{ $id}}">--}}
{{--                <input type="hidden" id="hidden_radius_value" value="{{ $radius}}">--}}
{{--                <span>{{ $id}}</span>--}}
{{--            </div>--}}
                            <input type="hidden" id="hidden_job_value" value="{{ $id}}">
                            <input type="hidden" id="hidden_radius_value" value="{{ $radius}}">
            @if($job)
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card border-left-3 border-left-slate rounded-left-0">
                            <div class="card-body">
                                <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                    <div>
                                        Title:<span class="font-weight-semibold">{{ $job['job_title'] }}</span>
                                        @if($cv_limit == $job['send_cv_limit'])
                                            <span class="badge badge-danger" style="font-size:90%">Limit Reached</span>
                                        @else
                                            <span class='badge badge-success' style='font-size:90%'>{{$job['send_cv_limit'] - $cv_limit." Cv's limit remaining  "}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        @endif
                                        <ul class="list list-unstyled mb-0">
                                            <li>Postcode: <span class="font-weight-semibold">{{ $job['postcode'] }}</span>
                                            </li>
                                            <li>Type: <span class="font-weight-semibold">{{ $job['job_type'] }}</span></li>
                                            <li>Head Office: <span
                                                    class="font-weight-semibold">{{ $job['name'] }}</span></li>
                                            <li>Qualification: <span
                                                    class="font-weight-semibold">{{ $job['qualification'] }}</span></li>
                                        </ul>
                                    </div>

                                    <div class="text-sm-right mb-0 mt-3 mt-sm-0 ml-auto">
                                        Salary:<span class="font-weight-semibold">{{ $job['salary'] }}</span>
                                        <ul class="list list-unstyled mb-0">
                                            <li>Categroy: <span class="font-weight-semibold">{{ $job['job_category'] }}</span>
                                            </li>
                                            <li>Experience: <span class="font-weight-semibold">{{ $job['experience'] }}</span>
                                            </li>
                                            <li>Unit: <span class="font-weight-semibold">{{ $job['unit_name'] }}</span>
                                            </li>
                                            <li class="dropdown">
                                                Status: &nbsp;
                                                <a href="#" class="badge badge-success align-top ">{{ $job['status'] }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer d-sm-flex justify-content-sm-between align-items-sm-center">
                                        <span>
                                            Sent CV: <span class="font-weight-semibold">{{ $sent_cv_count }} out of {{ $job['send_cv_limit'] }}</span>
                                        </span>

                                <ul class="list-inline list-inline-condensed mb-0 mt-2 mt-sm-0">
                                    <li class="list-inline-item">
                                        Posted On:<span class="font-weight-semibold">{{ $job['sale_added_date'] }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Default ordering -->
                <div class="col-md-12">
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger border-0 alert-dismissible mb-0 p-2">
                            <button type="button" class="close p-2" data-dismiss="alert"><span>×</span></button>
                            <span class="font-weight-semibold">Error!</span> {{ $message }}
                        </div>
                    @endif
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success border-0 alert-dismissible mb-0 p-2">
                            <button type="button" class="close p-2" data-dismiss="alert"><span>×</span></button>
                            <span class="font-weight-semibold">Success!</span> {{ $message }}
                        </div>
                    @endif
                </div>
                <div class="card-header header-elements-inline">

{{--                    <h5 class="card-title">Active Clients Within 8KM</h5>--}}
{{--                    <p></p>--}}

{{--                    @can('applicant_export')--}}
{{--                        <a href="{{ route('export_15km_applicants',['id' => $sale_export_id]) }}" class="btn bg-slate-800 legitRipple float-right" style="margin-right:20px;">--}}
{{--                            <i class="icon-cloud-upload"></i>--}}
{{--                            &nbsp;Export</a>--}}
{{--                    @endcan--}}
                </div>
                <div id="import_applicant_cv" class="modal fade">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Import CV</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('import_applicantCv') }}" method="post" enctype="multipart/form-data">
                                    @csrf()
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <input type="file" name="applicant_cv" class="file-input-advanced" data-fouc>
                                        </div>
                                    </div>

                                    <div class="modal-body-id">
                                        <input type="hidden" name="page_url" id="page_url" value="{{url()->current()}}"/>
                                    </div>
                                    <div class="modal-body-id">
                                        <input type="hidden" name="applicant_id" id="applicant_id" value=""/>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" style="background-color: purple">
                        <h5 class="card-title" style="color: white">Active Clients Within 8KM</h5>
                        <p></p>

                    </div>

                    <label></label><br>
                    @if($sent_cv_count < $job['send_cv_limit'])
                        <table class="table table-hover table-striped" id="applicants_15km_sample">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Postcode</th>
                                <th>Phone#</th>
                                <th>Applicant CV</th>
                                <th>Updated CV</th>
{{--                                <th>Upload CV</th>--}}
                                <th>Landline#</th>
                                <th>Source</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    @else
                        <h4 class="font-weight-semibold text-center mt-3">Send CV Limit for this Sale has reached maximum. Kindly increase Send CV Limit to send any CV on this Sale. Thank You</h4>
                        @if (!empty($active_applicants))
                            <table class="table table-hover table-striped datatable-sorting">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Name</th>
                                    <th>Postcode</th>
                                    <th>Stage</th>
                                    <th>Sub Stage</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($history_stages = config('constants.history_all_positive_stages'))
                                @foreach($active_applicants as $applicant)
                                    <tr>
                                        <td>{{ $applicant['history_added_date'] }}</td>
                                        <td>{{ $applicant['history_added_time'] }}</td>
                                        <td>{{ $applicant['applicant_name'] }}</td>
                                        <td>{{ $applicant['applicant_postcode'] }}</td>
                                        <td>{{ strtoupper($applicant['stage']) }}</td>
                                        <td>{{ $history_stages[$applicant['sub_stage']] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    @endif
                </div>
                <!-- /default ordering -->
            @else
                <div class="card">
                    <h4 class="text-center mt-2">Following job is either <span class="font-weight-semibold">pending</span> or <span class="font-weight-semibold">rejected</span>. Kindly contact your supervisor to activate this job. Thank You.</h4>
                </div>
            @endif
        </div>
        <!-- /content area -->

        @endsection
        @section('script')
            <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // alert('12');
                    var sentCvCount = {{ $sent_cv_count }};
                    var sendCvLimit = {{ $job['send_cv_limit'] }};

                    if (sentCvCount >= sendCvLimit) {
                        document.getElementById('applicants_15km_sample').style.display = 'none';
                    } else {
                        document.getElementById('active_applicants_table').style.display = 'none';
                    }

                });
            </script>
            <script>
                $(document).ready(function() {
                    // Calculate progress percentage
                    var cvLimit = parseInt('{{$job['send_cv_limit']}}'); // Convert string to number
                    var cvSent = parseInt('{{$cv_limit}}');
                    var progressPercentage = Math.floor((cvSent / cvLimit) * 100);

// Update progress bar and remaining count dynamically
                    var progressBar = document.querySelector('.progress-bar');
                    var remainingCount = document.querySelector('.badge');
                    progressBar.style.width = progressPercentage + '%';
                    remainingCount.textContent = cvLimit - cvSent + ' CV\'s remaining';

                    $.fn.dataTable.ext.errMode = 'none';
                    var job = $("#hidden_job_value").val();
                    // alert(job);
                    var radius = $("#hidden_radius_value").val();
                    // alert(job);
                    $('#applicants_15km_sample').DataTable({
                        "processing": false,
                        "serverSide": false,
                        // "responsive": true,

                        "ajax":"{!! url('get15kmApplicantsAjax') !!}/"+job+"/"+radius,
                        "order": [],
                        "columns": [
                            { "data":"updated_at", "name": "updated_at"},
                            { "data":"applicant_added_time", "name": "applicant_added_time", "orderable": false },
                            { "data":"app_name", "name": "clients.app_name" },
                            { "data":"app_email", "name": "clients.app_email" },
                            { "data":"applicant_job_title", "name": "clients.app_job_title" },
                            { "data":"app_job_category", "name": "clients.app_job_category" },
                            { "data":"app_postcode", "name": "clients.applicant_postcode", "orderable": true },
                            { "data":"app_phone", "name": "clients.app_phone" },
                            { "data":"download", "name": "clients.download", "orderable": false  },
                            { "data":"updated_cv", "name": "clients.updated_cv", "orderable": false  },
                            // { "data":"upload", "name": "clients.upload", "orderable": false  },
                            { "data":"app_phoneHome", "name": "clients.app_phoneHome" },
                            { "data":"app_source", "name": "clients.app_source" },
                            { "data":"applicant_notes", "name": "clients.app_notes" },
                            { "data":"status", "name": "clients.app_status" },
                            { "data":"action", "name": "action", "orderable": false}
                        ],
                        "rowCallback": function( row, data ) {
                            var dateCell = data.updated_at;
                            var sortedDate = dateSorting (dateCell);
                            $('td:eq(0)', row).html(sortedDate);
                        }
                    });

                });
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
