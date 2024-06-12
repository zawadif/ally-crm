@extends('layouts.master')
@section('title','Sales Management')
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
                        <h1>Sale Create</h1>
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

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-10 offset-md-1">
                                                    <div class="header-elements-inline">
{{--                                                        <h5 class="card-title">Add an Office</h5>--}}
{{--                                                        <a href="{{ route('offices.index') }}" class="btn bg-slate-800 legitRipple">--}}
{{--                                                            <i class="icon-cross"></i> --}}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <form id="submitData" method="POST" action="{{ route('sales.store') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="app_job_category" class="form-label">Job Category</label>
                                                        <select class="form-control" id="app_job_category" name="app_job_category" >
                                                            <option value="nurses">Nurses</option>
                                                            <option value="non-nurses">Non-Nurses</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="job_title" class="form-label">Job Title</label>
                                                        <select class="form-control" id="job_title" name="job_title"></select>
                                                        <span id="job_title_error" class="text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="row mb-6">
                                                    <div class="col-md-6">
                                                        <label for="job_title" class="form-label">Job Title special</label>
                                                        <select class="form-control" id="job_title_special" name="job_title_special"></select>

                                                        <span id="note_error" class="text-danger"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="benefits" class="form-label">Benefits</label>
                                                        <input type="text" class="form-control" id="benefits" name="benefits" value="{{ old('benefits') }}">
                                                        <span id="benefits_error" class="text-danger"></span>
                                                    </div>

                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="head_office" class="form-label">Franchises</label>
                                                        <select class="form-control" id="head_office" name="head_office">
                                                            @foreach($head_offices as $office)
                                                                <option value="{{$office->id}}">{{ucfirst($office->name)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="head_office_unit" class="form-label">Franchises Unit</label>
                                                        <select id="head_office_unit" name="head_office_unit" class="form-control">

                                                        </select>
                                                        <span id="head_office_unit_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="postcode" class="form-label">Postcode</label>
                                                        <input type="text" class="form-control" id="postcode" name="postcode" value="{{ old('postcode') }}">
                                                        <span id="postcode_error" class="text-danger"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="experience" class="form-label">Experience</label>
                                                        <input type="text" class="form-control" id="experience" name="experience" value="{{ old('experience') }}">
                                                        <span id="experience_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="salary" class="form-label">Job Type</label>
                                                        <input type="text" class="form-control" id="job_type" name="job_type" value="{{ old('job_type') }}">
                                                        <span id="job_type_error" class="text-danger"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="salary" class="form-label">Salary</label>
                                                        <input type="text" class="form-control" id="salary" name="salary" value="{{ old('salary') }}">
                                                        <span id="salary_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="qualification" class="form-label">Qualification</label>
                                                        <input type="text" class="form-control" id="qualification" name="qualification" value="{{ old('qualification') }}">
                                                        <span id="qualification_error" class="text-danger"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="salary" class="form-label">Cv Limit</label>
                                                        <input type="text" class="form-control" id="cv_limit" name="cv_limit" value="{{ old('cv_limit') }}">
                                                        <span id="cv_limit_error" class="text-danger"></span>
                                                    </div>
                                                    <!-- Add more fields here... -->
                                                </div>

                                                <div class="row mb-6">
                                                    <div class="col-md-6">
                                                        <label for="experience" class="form-label">Time</label>
                                                        <input type="text" class="form-control" id="time" name="time" value="{{ old('time') }}">
                                                        <span id="time_error" class="text-danger"></span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="note" class="form-label">Note</label>
                                                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                                                        <span id="note_error" class="text-danger"></span>
                                                    </div>
                                                </div><br>

                                                <!-- You can adjust styling and layout as needed -->
                                                <button type="submit" class="btn btn-primary greenButton" style="float: right">Submit</button>
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
                    'rgn_specialist': 'RGN Specialist',
                    'rmn_specialist': 'RMN Specialist',
                    // Add other job titles specific to 'nurse specialist'
                },
                'nonnurse specialist': {
                    'nonnurse_specialist_1': 'NonNurse Specialist 1',
                    'nonnurse_specialist_2': 'NonNurse Specialist 2',
                    // Add other job titles specific to 'nonnurse specialist'
                }

            };

            $('#app_job_category').change(function() {
                const selectedCategory = $('#app_job_category').val();
                // alert(selectedCategory);

                // const jobTitleOptions = jobTitles[selectedCategory];
                const jobTitleOptions = jobTitles[selectedCategory];

                if (jobTitleOptions) {
                    // console.log('Job Title Options:', jobTitleOptions);

                    const jobTitleDropdown = $('#job_title');
                    jobTitleDropdown.empty();

                    $.each(jobTitleOptions, function(key, value) {
                        jobTitleDropdown.append($('<option></option>').attr('value', key).text(value));
                    });
                } else {
                    console.log('Invalid Category');
                }

            });
            // $('#job_title').change(function() {
            //     const selectedTitle = $(this).val();
            //
            //     // Check if selectedTitle is 'nurse specialist' or 'nonnurse specialist'
            //     if (selectedTitle === 'nurse specialist' || selectedTitle === 'nonnurse specialist') {
            //         const jobTitleOptions = jobTitles[selectedTitle];
            //
            //         if (jobTitleOptions) {
            //             const jobTitleDropdownSpecial = $('#job_title_special');
            //             jobTitleDropdownSpecial.empty();
            //
            //             $.each(jobTitleOptions, function(key, value) {
            //                 jobTitleDropdownSpecial.append($('<option></option>').attr('value', key).text(value));
            //             });
            //         } else {
            //             console.log('Invalid Specialist Category');
            //         }
            //     }
            // });
            $('#job_title').change(function() {
                const selectedTitle = $(this).val();

                // Check if selectedTitle is 'nurse specialist' or 'nonnurse specialist'
                if (selectedTitle === 'nurse specialist' || selectedTitle === 'nonnurse specialist') {
                    // Make an AJAX request to get the job titles based on the selected category
                    $.ajax({
                        url: '/get-special-titles/' + selectedTitle,
                        type: 'GET',
                        success: function(response) {
                            const jobTitleDropdownSpecial = $('#job_title_special');
                            jobTitleDropdownSpecial.empty();

                            // Append fetched titles to the dropdown
                            $.each(response.titles, function(id, title) {
                                jobTitleDropdownSpecial.append($('<option></option>').attr('value', id).text(title));
                            });
                        },
                        error: function(error) {
                            console.error('Error fetching job titles:', error);
                        }
                    });
                }
            });
            $('#app_job_category').trigger('change');

        });


    </script>
    <script>
        $(document).ready(function() {
            // Change event for head_office dropdown
            $('#head_office').change(function() {
                var headOfficeId = $(this).val();

                // AJAX request to fetch units based on head office
                $.ajax({
                    type: 'GET',
                    url: '/get-head-units/' + headOfficeId, // Replace with your actual route
                    success: function(data) {
                        // Clear existing options
                        $('#head_office_unit').empty();

                        // Append new options
                        $.each(data.units, function(index, unit) {
                            $('#head_office_unit').append('<option value="' + unit.id + '">' + unit.unit_name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error fetching units: ' + xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#submitData').submit(function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Reset error messages
                $('.text-danger').text('');

                // Perform AJAX form submission
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        // Show SweetAlert success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Your form has been submitted successfully.',
                        });

                        // Handle success, if needed
                        window.location.href = '/sales';

                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        if (xhr.status == 422) { // Validation error
                            var errors = xhr.responseJSON.errors;

                            // Display errors below each input field
                            $.each(errors, function(field, message) {
                                $('#' + field + '_error').text(message[0]);
                            });

                            // Show SweetAlert error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please fix the errors in the form.',
                            });
                        } else {
                            console.error(xhr.responseText);

                            // Show SweetAlert error message for other errors
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again later.',
                            });
                        }
                    }
                });
            });
        });
    </script>




@endsection
