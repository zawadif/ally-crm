@extends('layouts.master')
@section('title','Client Management')
@section('style')
{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>--}}

    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>


    <style>
        .page-item.active .page-link {
            /*z-index: 3;*/
            color: white;

            background-color:#484646;
            border-color: #484646;
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
                        <h1>Client Management</h1>
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

                            <div class="card-header" style="background-color: #5b5858">

                                <h3 class="card-title" style="color: white">Clients Members</h3>

                                @can('applicant_import')
                                    <a href="#" style="color: white"
                                       data-controls-modal="#import_applicant_csv"
                                       data-backdrop="static"
                                       data-keyboard="false" data-toggle="modal"
                                       data-target="#import_applicant_csv" class="btn btn-sm btn-primary  float-right">
                                        <i class="icon-cloud-download"></i>
                                        &nbsp;Import</a>
                                    &nbsp;&nbsp;
                                @endcan
                                @can('applicant_create')
                                <div style="float: right">
                                <a  href="{{route('clients.create')}}"  class="btn btn-sm btn-primary greenButton"><i class="fa fa-user-nurse"></i> Add Client</a>
                                </div>
                                @endcan

                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-hover table-striped" id="applicant_sample_1">
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
                                        <th>Upload CV</th>
                                        <th>Landline#</th>
                                        <th>Source</th>
                                        <th>Notes</th>

                                        <!-- <th>Updated By</th> -->
                                        @canany(['applicant_edit','applicant_view','applicant_history','applicant_note-create','applicant_note-history'])
                                            <th>Action</th>
                                        @endcanany
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
{{--//table code--}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @can('applicant_import')
        <div id="import_applicant_csv" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Applicant CSV</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('applicantCsv') }}" method="post" enctype="multipart/form-data">
                            @csrf()
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="file" name="applicant_csv" class="file-input-advanced" data-fouc>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan



    {{--    <div id="import_applicant_cv" class="modal fade">--}}
{{--        <div class="modal-dialog modal-lg">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title">Import CV</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form action="{{ route('import_applicantCv') }}" method="post" enctype="multipart/form-data">--}}
{{--                        @csrf()--}}
{{--                        <div class="form-group row">--}}
{{--                            <div class="col-lg-12">--}}
{{--                                <input type="file" name="applicant_cv" class="file-input-advanced" data-fouc>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="modal-body-id">--}}
{{--                            <input type="hidden" name="page_url" id="page_url" value="{{url()->current()}}"/>--}}
{{--                        </div>--}}
{{--                        <div class="modal-body-id">--}}
{{--                            <input type="text" name="applicant_id" id="applicant_id" value=""/>--}}
{{--                        </div>--}}
{{--                        <button type="submit">Save</button>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <!-- Add this to your Blade view where you define the modal -->
    <div class="modal fade" id="import_applicant_cv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload CV</h5>
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
    <!-- Modal for Notes History -->
    <div class="modal fade" id="notesHistoryModal" tabindex="-1" role="dialog" aria-labelledby="notesHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="notesHistoryModalLabel">Notes History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content to display notes history will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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
        var columns = [
            { "data":"applicant_added_date", "name": "applicant_added_date" },
            { "data":"applicant_added_time", "name": "applicant_added_time" },
            { "data":"app_name", "name": "app_name", "orderable": true , "searchable": true },
            { "data":"app_email", "name": "app_email", "orderable": true, "searchable": true  },
            { "data":"applicant_job_title", "name": "app_job_title", "orderable": false },
            { "data":"app_job_category", "name": "app_job_category" },
            { "data":"app_postcode", "name": "app_postcode", "searchable": true  },
            { "data":"app_phone", "name": "app_phone" },
            { "data":"download", "name": "download", "orderable": false },
            { "data":"upload_cv", "name": "upload_cv", "orderable": false },
            { "data":"upload", "name": "upload", "orderable": false },
            { "data":"app_phoneHome", "name": "app_phoneHome" },
            { "data":"app_source", "name": "app_source" },
            { "data":"applicant_notes", "name": "applicant_notes" }
        ];

        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
                <?php if (\Illuminate\Support\Facades\Auth::user()->hasAnyPermission(['applicant_edit','applicant_view','applicant_history','applicant_note-create','applicant_note-history'])): ?>
            columns.push({ "data":"action", "name": "action" })
            <?php endif; ?>
            $('#applicant_sample_1').DataTable({

                "processing": true,
                "serverSide": true,
                "responsive": true,
                "searching": true,
                "ajax":"getApplicants",
                "order": [],
                "columns": columns

            });

        });

    </script>
    <script>
        function confirmDelete(clientId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If the user confirms, make the delete request
                    deleteClient(clientId);
                }
            });
        }

        function deleteClient(clientId) {
            axios.delete('/clients/' + clientId)
                .then(function (response) {
                    Swal.fire(
                        'Deleted!',
                        'Client has been deleted.',
                        'success'
                    );
                    // Optionally, you can redirect or perform additional actions here
                    location.reload(); // For example, reload the page
                })
                .catch(function (error) {
                    console.error('Delete request failed:', error);
                    Swal.fire(
                        'Error!',
                        'Unable to delete the client. Please try again.',
                        'error'
                    );
                });
        }
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
                        location.reload();

                    },
                    error: function (xhr, status, error) {
                        // Handle errors here
                        console.error();
                    }
                });
            });

            $('.modal-footer .btn-secondary').on('click', function () {
                // Hide the modal
                $('#import_applicant_cv').modal('hide');
            });

            // Ensure the modal is hidden on modal close
            $('#import_applicant_cv').on('hidden.bs.modal', function () {
                // Reset the form on modal close
                $('#cvUploadForm')[0].reset();
            });
        });


    </script>
    <script>
        function openNotesHistoryModal(applicantId) {
            // You can use AJAX to fetch notes history for the given applicantId
            // and then populate the modal body with the retrieved data
            // For example:
            $.ajax({
                url: '/notes/history/' + applicantId,
                type: 'GET',
                success: function(response) {
                    // Assuming the response contains HTML data to display notes history
                    $('#notesHistoryModal .modal-body').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    // Handle error
                }
            });
        }
    </script>



@endsection
