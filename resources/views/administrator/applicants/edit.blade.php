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
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
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
                        <h1>Team Management</h1>
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

                        <div class="">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-10 offset-md-1">
                                                    <div class="header-elements-inline">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <form id="submitData" method="POST" action="{{ route('clients.update',$applicant->id) }}" enctype="multipart/form-data">
                                                @csrf
@method('PATCH')
                                                <div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="app_name" class="form-label">Name</label>
                                                            <input type="text" class="form-control" id="app_name" name="app_name" value="{{$applicant->app_name}}">
                                                            <span id="app_name_error" class="text-danger"></span>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="app_email" class="form-label">Email</label>
                                                            <input type="text" class="form-control" id="app_email" name="app_email" value="{{$applicant->app_email}}">
                                                            <span id="app_email_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="app_phone" class="form-label">Phone</label>
                                                            <input type="tel" class="form-control" id="app_phone" name="app_phone" value="{{$applicant->app_phone}}">
                                                            <span id="app_phone_error" class="text-danger"></span>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="app_phoneHome" class="form-label">Home Phone</label>
                                                            <input type="tel" class="form-control" id="app_phoneHome" name="app_phoneHome" value="{{$applicant->app_phoneHome}}">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
{{--                                                            {{dd($applicant->app_job_category=="nurse")}}--}}

                                                            <label for="app_job_category" class="form-label">Job Category</label>
                                                            <select class="form-control" id="app_job_category" name="app_job_category">
                                                                @if($applicant->app_job_category=="nurses")
                                                                    <option value="{{$applicant->app_job_category}}">Qualified</option>
                                                                    <option value="non-nurses">Non-Qualified</option>

                                                                @else
                                                                 <option value="{{$applicant->app_job_category}}">Non-Qualified</option>
                                                                <option value="nurses">Qualified</option>
                                                                @endif
                                                            </select>

                                                            {{--                                                        <label for="app_job_title" class="form-label">Job Title</label>--}}
                                                            {{--                                                        <input type="text" class="form-control" id="app_job_title">--}}
                                                        </div>

                                                        {{--                                                    <div class="row mb-3">--}}
                                                        <div class="col-md-6">
                                                            <label for="job_title" class="form-label">Job Title</label>
                                                            <select class="form-control" id="job_title" name="job_title"></select>
                                                        </div>
                                                        <!-- Other input fields -->
                                                        {{--                                                    </div>--}}
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="app_source" class="form-label">Source</label>
                                                            <select name="app_source" id="app_source" class="form-control">
{{--                                                                <option value="{{$applicant->app_source}}" disabled >{{$applicant->app_source}}</option>--}}

                                                                @foreach($applicant_source as $source)
                                                                    <option value="{{$source}}" @if($source == $applicant->app_source) selected @endif>{{$source}}</option>
                                                                @endforeach
                                                            </select>
                                                            {{--                                                        <input type="text" class="form-control" id="app_source" name="app_source">--}}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="job_title" class="form-label">Job Title special</label>
                                                            <select class="form-control" id="job_title_special" name="job_title_special"></select>
                                                        </div>

                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="app_postcode" class="form-label">Postcode</label>
                                                            <input type="text" class="form-control" id="app_postcode" name="app_postcode" value="{{$applicant->app_postcode}}">
                                                            <span id="app_postcode_error" class="text-danger"></span>

                                                        </div>

                                                        <div class="col-md-6">
                                                            <label for="applicant_cv" class="form-label">CV</label>
                                                            <input type="file" class="form-control" id="applicant_cv" name="applicant_cv" >
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-12">
                                                            <label for="applicant_notes" class="form-label">Notes</label>
                                                            <textarea class="form-control" id="applicant_notes" name="applicant_notes" rows="3">{{$applicant->applicant_notes}}</textarea>
                                                            <span id="applicant_notes_error" class="text-danger"></span>

                                                        </div>
                                                    </div>
                                                    <!-- You can adjust styling and layout as needed -->
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
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

    <script>
        $(document).ready(function() {
            const jobTitles = {
                'nurses': {
                    'rgn': 'RGN',
                    'rmn': 'RMN',
                    'rnld': 'RNLD',
                    'senior nurse': 'Senior Nurse',
                    'nurse deputy manager': 'Nurse Deputy Manager',
                    'nurse manager': 'Nurse Manager',
                    'rgn/rmn': 'RGN/RMN',
                    'rmn/rnld': 'RMN/RNLD',
                    'rgn/rmn/rnld': 'RGN/RMN/RNLD',
                    'clinical lead': 'Clinical Lead',
                    'rcn': 'RCN',
                    'peripatetic nurse': 'Peripatetic Nurse',
                    'unit manager': 'Unit Manager',
                    'nurse specialist': 'Nurse Specialist'
                },
                'non-nurses': {
                    'care assistant': 'Care Assistant',
                    'senior care assistant': 'Senior Care Assistant',
                    'team lead': 'Team Lead',
                    'deputy manager': 'Deputy Manager',
                    'registered manager': 'Registered Manager',
                    'support worker': 'Support Worker',
                    'senior support worker': 'Senior Support Worker',
                    'activity coordinator': 'Activity Coordinator',
                    'nonnurse specialist': 'NonNurse Specialist'
                },
                'nurse specialist': {

                    // Add other job titles specific to 'nurse specialist'
                },
                'nonnurse specialist': {

                    // Add other job titles specific to 'nonnurse specialist'
                }
            };

            // Set selected values for job_category dropdown
            const selectedCategory = "{{$applicant->app_job_category}}";
            $('#app_job_category').val(selectedCategory).change(); // Set the selected value and trigger change event

            // Set selected values for job_title dropdown based on job_category
            const selectedJobTitle = "{{$applicant->app_job_title}}";
            $('#app_job_category').change(function() {
                const selectedCategory = $(this).val();
                const jobTitleOptions = jobTitles[selectedCategory];
                const jobTitleDropdown = $('#job_title');
                jobTitleDropdown.empty();

                if (jobTitleOptions) {
                    $.each(jobTitleOptions, function(key, value) {
                        jobTitleDropdown.append($('<option></option>').attr('value', key).text(value));
                    });

                    if (selectedCategory === "nurses" || selectedCategory === "non-nurses") {
                        jobTitleDropdown.val(selectedJobTitle); // Set the selected value for job_title dropdown
                    }
                }
            }).change(); // Trigger change event initially

            // Set selected values for job_title_special dropdown
            const selectedJobTitleSpecial = "{{$applicant->app_job_title_prof}}";
            $('#job_title').change(function() {
                const selectedTitle = $(this).val();

                if (selectedTitle === 'nurse specialist' || selectedTitle === 'nonnurse specialist') {
                    // Make an AJAX request to get the job titles special based on the selected title
                    $.ajax({
                        url: '/get-special-titles/' + selectedTitle,
                        type: 'GET',
                        success: function(response) {
                            const jobTitleDropdownSpecial = $('#job_title_special');
                            jobTitleDropdownSpecial.empty();
console.log(response);
                            // Append fetched titles to the dropdown
                            $.each(response.titles, function(id, title) {
                                jobTitleDropdownSpecial.append($('<option></option>').attr('value', id).text(title));
                            });

                            // Set the selected value for job_title_special dropdown
                            jobTitleDropdownSpecial.val("{{$applicant->app_job_title_prof}}");
                        },
                        error: function(error) {
                            console.error('Error fetching job titles:', error);
                        }
                    });
                }
            }).change(); // Trigger change event initially
        });

        $(document).ready(function() {
            $('#submitData').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle successful form submission
                        console.log(response); // Log the response
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Form submitted successfully.'
                        });
                        window.location.href = '/clients';
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'warning!',
                            text: 'Form submitted unsuccessfully.'
                        });
                        // Handle validation errors
                        var errors = xhr.responseJSON.errors;
                        console.log(errors); // Log validation errors

                        // Display errors on the form
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]); // Display the error near the respective input field
                        });
                    }
                });
            });
        });
    </script>




@endsection
