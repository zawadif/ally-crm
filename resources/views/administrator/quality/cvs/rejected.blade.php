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
                                <h3 class="card-title" style="color: white">Reject Cvs</h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-responsive" id="quality_reject_sample_1">
                                    <thead>
                                    <tr>
                                        <th>Date </th>
                                        <th>Time </th>
                                        <th>Sent By</th>
                                        <th>Name</th>
                                        <th>Job Title</th>
                                        <th>Postcode</th>
                                        <th>Phone#</th>
                                        <th>Landline#</th>
                                        @can('quality_CVs-Rejected_cv-download')
                                            <th>CV</th>
                                        @endcan
                                        @can('quality_CVs-Rejected_job-detail')
                                            <th>Job Details</th>
                                        @endcan
                                        <th>Head Office</th>
                                        <th>Unit</th>
                                        <th>Job Postcode</th>
                                        <th>Notes</th>
                                        <th >Action</th>
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
            { "data":"quality_added_date", "name": "quality_notes.created_at" },
            { "data":"quality_added_time", "name": "quality_notes.quality_added_time", "orderable": false},
            { "data":"fullName", "name": "users.fullName"},
            { "data":"app_name", "name": "clients.app_name" },
            { "data":"app_job_title_prof", "name": "app_job_title_prof" },
            { "data":"app_postcode", "name": "clients.app_postcode", "orderable": true },
            { "data":"app_phone", "name": "clients.app_phone", "orderable": true },
            { "data":"app_phoneHome", "name": "clients.app_phoneHome" }
        ];

            <?php if (\Illuminate\Support\Facades\Auth::user()->hasPermissionTo('quality_CVs-Rejected_cv-download')): ?>
        columns.push({ "data":"download", "name": "download", "orderable": false, "searchable": false });
        <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Auth::user()->hasPermissionTo('quality_CVs-Rejected_job-detail')): ?>
        columns.push({ "data":"job_details", "name": "job_details", "orderable": false, "searchable": false });
        <?php endif; ?>

        columns.push({ "data":"name", "name": "offices.name" ,"searchable": false});
        columns.push({ "data":"unit_name", "name": "units.unit_name" });
        columns.push({ "data":"postcode", "name": "sales.postcode" });
        columns.push({ "data":"details", "name": "quality_notes.details", "orderable": false });
        columns.push({ "data":"action", "name": "action", "orderable": false, "searchable": false });

        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#quality_reject_sample_1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":"get-reject-cv-applicants",
                "order": [[ 0, 'desc' ]],
                "columns": columns,
                "drawCallback": function( settings, json){
                    $('[data-popup="tooltip"]').tooltip();
                }
            });
        });

    </script>
    <script>
        function uploadCv(saleId) {
            showNotesUnHoldeModal(saleId);
        }

        // Function to show notes modal
        function showNotesUnHoldeModal(saleId) {
            $('#import_applicant_cv').modal('show');
            $('#applicant_file_id').val(saleId);  // Assuming your input field has the ID 'applicant_file_id'
        }

        $(document).ready(function () {
            // Click event for uploadCvBtn button
            $('#uploadCvBtn').on('click', function () {
                // Get the form data
                var form = $('#cvUploadForm')[0];
                var formData = new FormData(form);

                // Append the saleId to the formData
                formData.append('applicant_id', $('#applicant_file_id').val());

                // Make the AJAX request
                $.ajax({
                    url: '{{ route("import_applicantCv") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        var xhr = new XMLHttpRequest();

                        xhr.upload.onprogress = function (e) {
                            if (e.lengthComputable) {
                                var percent = (e.loaded / e.total) * 100;
                                $('.progress').show();
                                $('.progress-bar').css('width', percent + '%').attr('aria-valuenow', percent).text(percent.toFixed(0) + '%');
                            }
                        };

                        return xhr;
                    },
                    success: function (response) {
                        // Hide the modal on success
                        $('#import_applicant_cv').modal('hide');

                        // Show success message, e.g., using a library like SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'CV uploaded successfully!',
                        });
                        // location.reload();
                        // $('#applicant_sample_1').DataTable().ajax.reload();

                    },
                    error: function (xhr, status, error) {
                        // Handle errors here
                        console.error();
                    }
                });
            });
        });


    </script>



@endsection
