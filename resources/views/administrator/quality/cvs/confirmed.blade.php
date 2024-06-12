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
                                <h3 class="card-title" style="color: white">Cleared Cvs</h3>

                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-responsive table-striped" id="quality_cleared_sample_1">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Sent By</th>
                                        <th>Name</th>
                                        <th>Job Title</th>
                                        <th>Postcode</th>
                                        <th>Phone#</th>
                                        <th>Landline#</th>
                                        @can('quality_CVs-Cleared_cv-download')
                                            <th>CV</th>
                                        @endcan
                                        @can('quality_CVs-Cleared_job-detail')
                                            <th>Job Details</th>
                                        @endcan
                                        <th>Head Office</th>
                                        <th>Unit</th>
                                        <th>Job Postcode</th>
                                        <th>Notes</th>
{{--                                        <th >Action</th>--}}
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


@endsection

@section('script')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        var columns = [
            { "data":"quality_added_date", "name": "quality_notes.created_at" },
            { "data":"quality_added_time", "name": "quality_notes.quality_added_time", "orderable": false},
            { "data":"fullName", "name": "users.fullName"},
            { "data":"app_name", "name": "clients.app_name" },
            { "data":"app_job_title", "name": "clients.app_job_title" },
            { "data":"app_postcode", "name": "clients.app_postcode", "orderable": true },
            { "data":"app_phone", "name": "clients.app_phone", "orderable": true },
            { "data":"app_phoneHome", "name": "clients.app_phoneHome" }
        ];


            <?php if (\Illuminate\Support\Facades\Auth::user()->hasPermissionTo('quality_CVs-Cleared_cv-download')): ?>
        columns.push({ "data":"download", "name": "download", "orderable": false, "searchable": false });
        <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Auth::user()->hasPermissionTo('quality_CVs-Cleared_job-detail')): ?>
        columns.push({ "data":"job_details", "name": "job_details", "orderable": false, "searchable": false });
        <?php endif; ?>

        columns.push({ "data":"office_name", "name": "office_name" });
        columns.push({ "data":"unit_name", "name": "units.unit_name" });
        columns.push({ "data":"postcode", "name": "sales.postcode" });
        columns.push({ "data":"details", "name": "quality_notes.details", "orderable": false });
        // columns.push({ "data":"action", "name": "action", "orderable": false, "searchable": false });


        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#quality_cleared_sample_1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":"get-confirm-cv-applicants",
                "order": [[ 0, 'desc' ]],
                "columns": columns
            });
        });

    </script>


@endsection
