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
                        {{--                        <h1>Nurse Management</h1>--}}
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
                                <h3 class="card-title" style="color: white">Client 2 Months Added</h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-hover table-striped" id="last_2_months_sample">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Job Title</th>
                                        <th>Category</th>
                                        <th>Postcode</th>
                                        <th>Phone</th>
                                        <th>Applicant CV</th>
                                        <th>Updated CV</th>
                                        <th>Upload CV</th>
                                        <th>Landline</th>
                                        <th>Source</th>
                                        <th>Notes</th>
                                        <th>History</th>
                                        <th>Status</th>
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


    <div class="modal fade" id="import_applicant_cv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload CV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="cvUploadForm" action="{{ route('import_applicantCv') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="applicant_file_id" name="applicant_id" value="">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="applicant_cv" name="applicant_cv" required>
                            <label class="custom-file-label" for="applicant_cv">Choose file</label>
                        </div>
                        <div class="progress mt-3" style="display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="uploadCvBtn">Upload</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>


        {{--    // table.destroy();--}}

        {{--});--}}

        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#last_2_months_sample').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":"get2MonthsApplicants",
                "order": [[ 0, 'desc' ]],
                "columns": [
                    { "data":"updated_at", "name": "clients.updated_at" },
                    { "data":"applicant_added_time", "name": "applicant_added_time", "orderable": false },
                    { "data":"app_name", "name": "clients.app_name" },
                    { "data":"app_email", "name": "clients.app_email" },
                    { "data":"applicant_job_title", "name": "clients.app_job_title" },
                    { "data":"app_job_category", "name": "clients.app_job_category" },
                    { "data":"applicant_postcode", "name": "clients.app_postcode", "orderable": true },
                    { "data":"app_phone", "name": "clients.app_phone" },
                    { "data":"download", "name": "download", "orderable": false },
                    { "data":"updated_cv", "name": "updated_cv", "orderable": false },
                    { "data":"upload", "name": "upload", "orderable": false },
                    { "data":"app_phoneHome", "name": "clients.app_phoneHome" },
                    { "data":"app_source", "name": "clients.app_source" },
                    { "data":"applicant_notes", "name": "clients.applicant_notes" },
                    { "data":"history", "name": "history", "orderable": false },
                    { "data":"status", "name": "clients.app_status" }
                ]
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
                        $('#last_7_days_sample').DataTable().ajax.reload();
                        location.reload();

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
