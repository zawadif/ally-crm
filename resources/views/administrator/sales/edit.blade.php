@extends('layouts.master')
@section('title','Sale edit Management')
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
                        <h1>Sale Edit</h1>
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
                                                        {{--                                                        <h5 class="card-title">Add an Office</h5>--}}
                                                        {{--                                                        <a href="{{ route('offices.index') }}" class="btn bg-slate-800 legitRipple">--}}
                                                        {{--                                                            <i class="icon-cross"></i> --}}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <form id="submitData" method="POST" action="{{ route('sales.update',$sale->id) }}" enctype="multipart/form-data">
{{--                                                @csrf--}}
{{--                                                @method('PATCH')--}}
{{--                                            <form id="submitData" method="POST" action="{{ route('sales.store') }}" enctype="multipart/form-data">--}}
                                                @csrf
                                                @method('PATCH')
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
                                                        <input type="text" class="form-control" id="benefits" name="benefits" value="{{ $sale->benefits }}">
                                                        <span id="benefits_error" class="text-danger"></span>
                                                    </div>

                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="head_office" class="form-label">Franchises</label>
                                                        <select class="form-control" id="head_office" name="head_office">
                                                            @foreach($head_offices as $office)
                                                                <option value="{{$office->id}}" {{ $sale->head_office == $office->id ? 'selected' : '' }}>{{ucfirst($office->name)}}</option>
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
                                                        <input type="text" class="form-control" id="postcode" name="postcode" value="{{$sale->postcode }}">
                                                        <span id="postcode_error" class="text-danger"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="experience" class="form-label">Experience</label>
                                                        <input type="text" class="form-control" id="experience" name="experience" value="{{ $sale->experience }}">
                                                        <span id="experience_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="salary" class="form-label">Job Type</label>
                                                        <input type="text" class="form-control" id="job_type" name="job_type" value="{{ $sale->job_type }}">
                                                        <span id="job_type_error" class="text-danger"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="salary" class="form-label">Salary</label>
                                                        <input type="text" class="form-control" id="salary" name="salary" value="{{ $sale->salary }}">
                                                        <span id="salary_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="qualification" class="form-label">Qualification</label>
                                                        <input type="text" class="form-control" id="qualification" name="qualification" value="{{ $sale->qualification }}">
                                                        <span id="qualification_error" class="text-danger"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="salary" class="form-label">Cv Limit</label>
                                                        <input type="number" min="0" class="form-control" id="cv_limit" name="cv_limit" value="{{ $sale->send_cv_limit }}">
                                                        <span id="cv_limit_error" class="text-danger"></span>
                                                    </div>
                                                    <!-- Add more fields here... -->
                                                </div>

                                                <div class="row mb-6">
                                                    <div class="col-md-6">
                                                        <label for="experience" class="form-label">Time</label>
                                                        <input type="text" class="form-control" id="time" name="time" value="{{ $sale->time }}">
                                                        <span id="time_error" class="text-danger"></span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="note" class="form-label">Note</label>
                                                        <textarea class="form-control" id="note" name="note" rows="3">{{ $sale->sale_notes }}</textarea>
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
                    // 'rgn_specialist': 'RGN Specialist',
                    // 'rmn_specialist': 'RMN Specialist',
                    // Add other job titles specific to 'nurse specialist'
                },
                'nonnurse specialist': {

                    // Add other job titles specific to 'nonnurse specialist'
                }

            };

            $(document).ready(function() {
                // Add this section to set selected values for job_category dropdown
                const selectedCategory = "{{$sale->job_category}}";
                $('#app_job_category').val(selectedCategory).change(); // Set the selected value and trigger change event

                // Add this section to set selected values for job_title dropdown based on job_category
                const selectedJobTitle = "{{$sale->job_title}}";
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

                // Add this section to set selected values for job_title_special dropdown
                const selectedJobTitleSpecial = "{{$sale->job_title_prof}}";
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

                                // Append fetched titles to the dropdown
                                $.each(response.titles, function(id, title) {
                                    jobTitleDropdownSpecial.append($('<option></option>').attr('value', id).text(title));
                                });

                                // Set the selected value for job_title_special dropdown
                                jobTitleDropdownSpecial.val("{{$sale->job_title_prof}}");
                            },
                            error: function(error) {
                                console.error('Error fetching job titles:', error);
                            }
                        });
                    }
                }).change();
              // Trigger change event initially
            });

        });

        $(document).ready(function(){
            // Function to populate Head Office Units dropdown
            function populateHeadOfficeUnits(headOfficeId) {
                if(headOfficeId){
                    $.ajax({
                        url: '/get-head-units/' + headOfficeId, // Replace with your actual route
                        type: "GET",
                        dataType: "json",
                        success:function(data) {
                            var unit_data = data.units;
                            $('#head_office_unit').empty();
                            $.each(unit_data, function(key, value){
                                $('#head_office_unit').append('<option value="'+ value.id +'">'+ value.unit_name +'</option>');
                            });
                            // Set the selected unit based on the stored value
                            $('#head_office_unit').val("{{$sale->head_office_unit}}");
                        }
                    });
                } else {
                    $('#head_office_unit').empty();
                }
            }

            // Event listener for Head Office dropdown change
            $('#head_office').on('change', function(){
                var headOfficeId = $(this).val();
                populateHeadOfficeUnits(headOfficeId);
            });

            // Get stored values for head office and unit
            var storedHeadOfficeId = "{{$sale->head_office}}";
            var storedUnitId = "{{$sale->head_office_unit}}";

            // Populate head office dropdown with stored value
            $('#head_office').val(storedHeadOfficeId);

            // Trigger change event to populate units dropdown
            $('#head_office').trigger('change');
        });


        $(document).ready(function () {
            $('#submitData').on('submit', function (e) {
                e.preventDefault();

                // Clear existing error messages
                $('.text-danger').text('');

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
                    success: function (response) {
                        console.log(response);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Unit updated successfully.'
                        });
                        window.location.href = '/sales';
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Form submitted unsuccessfully.'
                        });
                        var errors = xhr.responseJSON.errors;
                        console.log(errors);

                        $.each(errors, function (key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    }
                });
            });
        });

    </script>





@endsection
