@extends('layouts.master')
@section('title','Clear Cv Management')
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
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('sent_cv*') ? 'active' : '' }}" href="{{ route('sent_cv') }}">Sent CVs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('crm-request_cv*') ? 'active' : '' }}" href="{{ route('crm-request_cv') }}">Requests</a>
                        </li>
                        <!-- Add more tabs as needed -->
                    </ul>
                    <!-- end category create form -->
                    <div class="col">

                        <div class="card">

                            <div class="card-header" style="background-color: purple">
                                <h3 class="card-title" style="color: white">Cleared Cvs </h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-hover table-striped" id="crm_sent_cv_sample">
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
            { "data":"quality_added_date", "name": "quality_notes.quality_added_date" },
            { "data":"quality_added_time", "name": "quality_notes.quality_added_time", "orderable": false },
            { "data":"agent_by", "name": "agent_by", "orderable": false, "searchable": false },
            { "data":"app_name", "name": "app_name" },
            { "data":"app_job_title", "name": "clients.app_job_title" },
            { "data":"app_postcode", "name": "clients.app_postcode" },
            { "data":"job_details", "name": "job_details", "orderable": false, "searchable": false },
            { "data":"office_name", "name": "office_name" },
            { "data":"unit_name", "name": "units.unit_name" },
            { "data":"postcode", "name": "sales.postcode" },
            { "data":"crm_note", "name": "crm_note" },
            { "data":"action", "name": "action", "orderable": false, "searchable": false }
        ];




        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#crm_sent_cv_sample').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":"crm-sent-cv",
                "order": [[ 0, 'desc' ]],
                "columns": columns
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
                        location.reload();
                        // $('#applicant_sample_1').DataTable().ajax.reload();

                    },
                    error: function (xhr, status, error) {
                        // Handle errors here
                        console.error();
                    }
                });
            });
        });

        $(document).on('click', '.sent_cv_submit', function (event) {
            // alert('request_cv_submit');

            event.preventDefault();
            console.log($(this).data('cv_modal_name'));

            // Check the value of model_name directly from the input field
            console.log($('input.model_name').val());
            var form_action = $(this).val();
            var app_sale = $(this).data('app_sale');
            var model_name = $('input.model_name').val();
// alert(model_name);
            // var model_name= $(this).find('input.model_name');
            var nev= $(this).closest(".model_name").val();

            var sent_cv_form = '';
            var sent_cv_alert = '';
            var details = '';

            if(model_name == 'sent_cv')
            {
                sent_cv_form = $('#sent_cv_form'+app_sale);
                sent_cv_alert = $('#sent_cv_alert' + app_sale);
                details = $.trim($("#sent_cv_details" + app_sale).val());
                console.log('sent cv '+sent_cv_form+' and , '+sent_cv_alert+' and ,'+details);
            }
            else if(model_name == 'sent_cv_nurse')
            {
                sent_cv_form = $('#sent_cv_form_nurse'+app_sale);
                sent_cv_alert = $('#sent_cv_alert_nurse' + app_sale);
                details = $.trim($("#sent_cv_details_nurse" + app_sale).val());
                console.log('sent cv Nurse '+sent_cv_form+' and , '+sent_cv_alert+' and ,'+details);

            }
            else
            {
                sent_cv_form = $('#sent_cv_form_non_nurse'+app_sale);
                sent_cv_alert = $('#sent_cv_alert_non_nurse' + app_sale);
                details = $.trim($("#sent_cv_details_non_nurse" + app_sale).val());
                console.log('sent cv non nurse '+sent_cv_form+' and , '+sent_cv_alert+' and ,'+details);

            }

            if (details) {
                $.ajax({
                    // url: "{{ route('sentCvAction') }}",
                    url: "sent-cv-action",
                    type: "POST",
                    data: sent_cv_form.serialize() + '&' + form_action + '=' + form_action,
                    success: function (response) {
                        console.log(response);

                        $('#crm_sent_cv_sample').DataTable().ajax.reload();
                        toastr.success(response.message);
                        sent_cv_alert.html(response);
                        setTimeout(function () {
                            $('#clear_cv' + app_sale).modal('hide');
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open");
                            $("body").removeAttr("style");
                        }, 1000);
                    },
                    error: function (response) {
                        var raw_html = '<p class="text-danger">WHOOPS! Something Went Wrong!!</p>';
                        sent_cv_alert.html(raw_html);
                    }
                });
            } else {
                sent_cv_alert.html('<p class="text-danger">Kindly Provide Details</p>');
            }
            sent_cv_form.trigger('reset');
            setTimeout(function () {
                sent_cv_alert.html('');
            }, 2000);
            return false;
        });
    </script>



@endsection
