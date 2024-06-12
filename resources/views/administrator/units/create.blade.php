@extends('layouts.master')
@section('title','Unit Management')
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
                        <h1>Unit Create</h1>
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
                                            <form id="submitData" method="POST" action="{{ route('units.store') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="unit_name" class="form-label">Unit Name</label>
                                                            <input type="text" class="form-control" id="unit_name" name="unit_name" value="{{ old('unit_name') }}">
                                                            <span id="unit_name_error" class="text-danger"></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="contact_name" class="form-label">Contact Name</label>
                                                            <input type="text" class="form-control" id="contact_name" name="contact_name" value="{{ old('contact_name') }}">
                                                            <span id="contact_name_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="office_phone" class="form-label">Contact Phone</label>
                                                            <input type="tel" class="form-control" id="office_phone" name="office_phone" value="{{ old('contact_phone_number') }}">
                                                            <span id="office_phone_error" class="text-danger"></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="office_phoneHome" class="form-label">Contact Landline</label>
                                                            <input type="tel" class="form-control" id="office_phoneHome" name="office_phoneHome" value="{{ old('contact_landline') }}">
                                                            <span id="office_phoneHome_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="job_type" class="form-label">Head Office</label>
                                                            <select class="form-control" id="head_office" name="head_office">
                                                                @foreach($head_offices as $office)
                                                                <option value="{{$office->id}}">{{ucfirst($office->name)}}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="office_postcode" class="form-label">Postcode</label>
                                                            <input type="text" class="form-control" id="office_postcode" name="office_postcode" value="{{ old('unit_postcode') }}">
                                                            <span id="office_postcode_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="office_email" class="form-label">Contact Email</label>
                                                            <input type="text" class="form-control" id="office_email" name="office_email" value="{{ old('contact_email') }}">
                                                            <span id="office_email_error" class="text-danger"></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="office_website" class="form-label">Website</label>
                                                            <input type="text" class="form-control" id="office_website" name="office_website" value="{{ old('website') }}">
                                                            <span id="office_website_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-12">
                                                            <label for="unit_notes" class="form-label">Notes</label>
                                                            <textarea class="form-control" id="unit_notes" name="unit_notes" rows="3">{{ old('unit_notes') }}</textarea>
                                                            <span id="unit_notes_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                    <!-- You can adjust styling and layout as needed -->
                                                    <button type="submit" class="btn btn-primary greenButton" style="float: right">Submit</button>
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
                        window.location.href = '/units';
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
