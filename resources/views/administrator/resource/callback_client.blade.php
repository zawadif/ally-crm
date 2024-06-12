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
                        {{--                        <h1>Team Management</h1>--}}
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
                                <h3 class="card-title" style="color: white">Callback Schedule</h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-hover table-striped" id="last_2_months_blocked_sample">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Name</th>
                                        <th>Job Title</th>
                                        <th>Category</th>
                                        <th>Postcode</th>
                                        <th>Phone</th>
                                        <th>Landline</th>
                                        <th>Source</th>
                                        <th>Notes</th>
{{--                                        <th>Status</th>--}}
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




@endsection

@section('script')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>

        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#last_2_months_blocked_sample').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":"get-call-back-clients",
                "order": [[ 0, 'desc' ]],
                "columns": [


                    { "data":"added_date", "name": "applicant_notes.added_date" },
                    { "data":"added_time", "name": "applicant_notes.added_time" },
                    { "data":"app_name", "name": "clients.app_name" },
                    { "data":"app_job_title", "name": "clients.app_job_title" },
                    { "data":"app_job_category", "name": "clients.app_job_category" },
                    { "data":"app_postcode", "name": "app_postcode", "orderable": true },
                    { "data":"app_phone", "name": "clients.app_phone" },
                    { "data":"app_phoneHome", "name": "clients.app_phoneHome" },
                    { "data":"app_source", "name": "clients.app_source" },
                    { "data":"details", "name": "applicant_notes.details" },
                    // { "data":"status", "name": "status", "orderable": false },
                    { "data":"action", "name": "action", "orderable": false }
                ]
            });

        });
        $(document).on('click', '.reject_history', function () {
            var applicant = $(this).data('applicant');
            $.ajax({
                url: "{{ route('rejectedHistory') }}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    applicant: applicant
                },
                success: function(response){
                    $('#applicant_rejected_history'+applicant).html(response);
                },
                error: function(response){
                    var raw_html = '<p>WHOOPS! Something Went Wrong!!</p>';
                    $('#applicant_rejected_history'+applicant).html(raw_html);
                }
            });
        });

    </script>
    <script>
        function confirmDelete(clientId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to Unblock this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Unblock it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If the user confirms, make the delete request
                    deleteClient(clientId);
                }
            });
        }

        function deleteClient(clientId) {
            axios.delete('/revertTempInterestAjax/' + clientId)
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



@endsection
