@extends('layouts.master')
@section('title','Team Management')
@section('style')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>


    <style>
        .page-item.active .page-link {
            /*z-index: 3;*/
            color: white;

            background-color:purple;
            border-color: purple;
        }
    </style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{--                        <h1>Pending Sales</h1>--}}
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- category create form -->

                    <!-- end category create form -->
                    <div class="col">

                        <div class="card">

                            <div class="card-header" style="background-color: purple">
                                <h3 class="card-title" style="color: white">Crm paid Cvs</h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-hover table-striped" id="crm_confirmation_cv_sample">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th data-popup="tooltip" title="Un-searchable, Un-sortable">Sent By</th>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Postcode</th>
                                        <th>Job Details</th>
                                        <th>Head Office</th>
                                        <th>Unit</th>
                                        <th>Job Postcode</th>
                                        <th>Notes</th>
                                        {{--                                        <th> Schedule</th>--}}
                                        {{--                                        <th>schedule_search</th>--}}
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('administrator.quality.cvs.mode_form')

@endsection

@section('script')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>


        var columns = [
            { "data":"crm_added_date", "name": "crm_notes.crm_added_date" },
            { "data":"crm_added_time", "name": "crm_notes.crm_added_time", "orderable": false },
            { "data":"name", "name": "users.fullName", "orderable": false, "searchable": false },
            { "data":"app_name", "name": "clients.app_name" },
            { "data":"applicant_job_title", "name": "applicant_job_title" },
            { "data":"app_postcode", "name": "clients.app_postcode" },
            { "data":"job_details", "name": "job_details", "orderable": false, "searchable": false },
            { "data":"office_name", "name": "office_name" },
            { "data":"unit_name", "name": "units.unit_name" },
            { "data":"postcode", "name": "sales.postcode" },
            { "data":"crm_note", "name": "crm_note" },
            // { "data":"interview_schedule", "name": "interview_schedule" },
            // { "data":"schedule_search", "name": "schedule_search" },
            { "data":"action", "name": "action", "orderable": false, "searchable": false }
        ];




        $(document).ready(function() {
            // $.fn.dataTable.ext.errMode = 'none';
            $('#crm_confirmation_cv_sample').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":"crm-paid",
                "order": [[ 0, 'desc' ]],
                "columns": columns
            });
        });




        $(document).on('click', '.paid_status_submit', function (event) {
            event.preventDefault();
            var form_action = $(this).val();
            var app_sale = $(this).data('app_sale');
            var $paid_status_form = $('#paid_status_form'+app_sale);
            var $paid_status_alert = $('#paid_status_alert' + app_sale);
            console.log($paid_status_form.serialize() + '&paid_status=' + form_action);

            if ((form_action === 'Open') || (form_action === 'Close')) {
                $.ajax({
                    url: "paid-action",
                    type: "POST",
                    data: $paid_status_form.serialize() + '&paid_status=' + form_action,
                    success: function (response) {
                        $('#crm_confirmation_cv_sample').DataTable().ajax.reload();
                        toastr.success(response.message);
                        $paid_status_alert.html(response);
                        setTimeout(function () {
                            $('#paid_status' + app_sale).modal('hide');
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open");
                            $("body").removeAttr("style");
                        }, 2000);
                    },
                    error: function (response) {
                        var raw_html = '<p class="text-danger">WHOOPS! Something Went Wrong!!</p>';
                        $paid_status_alert.html(raw_html);
                    }
                });
            } else {
                $paid_status_alert.html('<p class="text-danger">Form action do not match</p>');
            }
            $paid_status_form.trigger('reset');
            setTimeout(function () {
                $paid_status_alert.html('');
            }, 2000);
            return false;
        });

    </script>




@endsection
