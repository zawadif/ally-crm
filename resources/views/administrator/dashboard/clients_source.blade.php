@extends('layouts.master')
@section('title','Sales Management')
@section('style')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">


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
{{--                        <h1>Sales Management</h1>--}}
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
                                <h3 class="card-title" style="color: white">Candidates --{{$source_name}}</h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table" id="clients-table">
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
{{--                                        <th>Applicant CV</th>--}}
{{--                                        <th>Updated CV</th>--}}
{{--                                        <th>Upload CV</th>--}}
                                        <th>Landline#</th>
                                        <th>Source</th>
                                        <th>Notes</th>
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
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>

    <script>

        var columns = [
            { "data": "applicant_added_date", "name": "Date" },
            { "data": "applicant_added_time", "name": "Time" },
            { "data": "app_name", "name": "Name" },
            { "data": "app_email", "name": "Email" },
            { "data": "app_job_title", "name": "Title" },
            { "data": "app_job_category", "name": "Category" },
            { "data": "app_postcode", "name": "Postcode" },
            { "data": "app_phone", "name": "Phone#" },
            { "data": "app_phoneHome", "name": "Landline#" },
            { "data": "app_source", "name": "Source" },
            { "data": "applicant_notes", "name": "Notes" }
        ];

        $(document).ready(function() {
            $('#clients-table').DataTable({
                "processing": true,
                "serverSide": false, // Since data is already fetched from the server
                "data": <?php echo json_encode($clients); ?>, // Pass the fetched data
                "columns": columns
            });
        });


    </script>







@endsection
