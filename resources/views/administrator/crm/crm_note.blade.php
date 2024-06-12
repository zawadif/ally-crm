@extends('layouts.master')
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>
<style>
    .page-item.active .page-link {
        /*z-index: 3;*/
        color: white;

        background-color:purple;
        border-color: purple;
    }
</style>
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-dark has-cover" style="border: 1px solid #ddd; border-bottom: 0;">
            <div class="page-header-content header-elements-inline">
                <div class="page-title">
                    <h5>
                        <i class="icon-arrow-left52 mr-2"></i>
{{--                        <span class="font-weight-semibold">Crm</span> - Notes History--}}
                    </h5>
                </div>
            </div>


        </div>
        <!-- /page header -->

        <section class="content">
        <!-- Content area -->
        <div class="content">

            <!-- Invoice template -->
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h6 class="card-title">Note's History</h6>
                </div>
                @if((!empty($cv_send_in_quality_notes)) || (!empty($applicant_in_quality)) || (!empty($applicant_in_crm)))
                    <div class="card-body">
<input type="hidden" value="{{$client_id}}" name="client_id" id="client_id">
<input type="hidden" value="{{$sale_id}}" name="sale_id" id="sale_id">
                        <div class="row">

                            <div class="col-lg-3">
                                <span class="text-muted">CV Search Note:</span>
                                <div class="card border-top border-3 border-primary rounded-start">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            @empty($cv_send_in_quality_notes)
                                                <div>
                                                    No note found.
                                                </div>
                                            @else
                                                <div>
                                                    Date: <span class="font-weight-bold">{{ $cv_send_in_quality_notes->send_added_date }}</span>
                                                    <ul class="list-unstyled mb-0">
                                                        <li>Note: <span class="font-weight-bold">{{ $cv_send_in_quality_notes->details }}</span></li>
                                                    </ul>
                                                </div>

                                                <div class="ms-auto">
                                                    Time: <span class="font-weight-bold">{{ $cv_send_in_quality_notes->send_added_time }}</span>
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="dropdown">
                                                            Status: &nbsp;
                                                            <a href="#" class="badge bg-info">{{ $cv_send_in_quality_notes->status }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endempty
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <span class="text-muted">Quality Note:</span>
                                <div class="card border-top border-3 border-primary rounded-start">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            @empty($applicant_in_quality)
                                                <div>
                                                    No note found.
                                                </div>
                                            @else
                                                <div>
                                                    Date: <span class="font-weight-bold">{{ $applicant_in_quality->quality_added_date }}</span>
                                                    <ul class="list-unstyled mb-0">
                                                        <li>Note: <span class="font-weight-bold">{{ $applicant_in_quality->details }}</span></li>
                                                    </ul>
                                                </div>

                                                <div class="ms-auto">
                                                    Time: <span class="font-weight-bold">{{ $applicant_in_quality->quality_added_time }}</span>
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="dropdown">
                                                            Status: &nbsp;
                                                            <a href="#" class="badge bg-info">{{ $applicant_in_quality->moved_tab_to }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endempty
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body">
                                    <h5>Applicant's Note In CRM</h5>
                                </div>
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover datatable-sorting data_table" id="crmNotesTable">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Active In</th>
                                            <th>Notes</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body" style="text-align: center">
                        No Found Any Relevent Notes
                    </div>

                @endif
            </div>
            <!-- /invoice template -->

        </div>
        </section>
        <!-- /content area -->

@endsection()
@section('script')
{{--            <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>--}}
            <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
            <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('#crmNotesTable').DataTable({
                        "processing": true,
                        "serverSide": false,
                        "ajax": {
                            "url": "/crm-notes/{{ $client_id }}/{{ $sale_id }}/datatable",
                            "type": "GET",
                            "dataType": "json",
                            "dataSrc": ""
                        },
                        "columns": [
                            {
                                "data": null, // Use null for the Id column to generate sequential numbers
                                "render": function(data, type, row, meta) {
                                    // Return the row index + 1 as the Id
                                    return meta.row + 1;
                                }
                            },
                            {
                                "data": "crm_added_date",
                                "render": function(data) {
                                    return formatDate(data); // Format date
                                }
                            },
                            {
                                "data": "crm_added_time",
                                "render": function(data) {
                                    return formatTime(data); // Format time
                                }
                            },
                            { "data": "moved_tab_to" },
                            { "data": "details" },
                            {
                                "data": "status",
                                "render": function(data) {
                                    return (data == 'active') ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Disable</span>';
                                }
                            }
                        ],
                        "language": {
                            "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                        },
                        "rowCallback": function(row, data, index) {
                            $('td:eq(0)', row).html(index + 1); // Update the first column with sequential numbers
                        }
                    });
                });

                function formatDate(dateStr) {
                    var date = new Date(dateStr);
                    var options = { day: 'numeric', month: 'short', year: 'numeric' };
                    return date.toLocaleDateString('en-US', options);
                }

                function formatTime(timeStr) {
                    var time = new Date("2000-01-01T" + timeStr); // Assuming time is in "HH:mm:ss" format
                    var options = { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
                    return time.toLocaleTimeString('en-US', options);
                }

            </script>
@endsection
