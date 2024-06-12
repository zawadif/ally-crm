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
                                <h3 class="card-title" style="color: white">Open Sales </h3>
                                <input type="hidden" value="{{$startDate}}" id="startDate" name="startDate">
                                <input type="hidden" value="{{$endDate}}" id="endDate" name="endDate">

                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table" id="sale-open-table">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Agent by</th>
                                        <th>Job Category</th>
                                        <th>Job Title</th>
                                        <th>Office Name</th>
                                        <th>Unit Name</th>
                                        <th>Postcode</th>
                                        <th>Job Type</th>
                                        <th>Experience</th>
{{--                                        <th>Qualification</th>--}}
                                        <th>Salary</th>
                                        <th>Cv limit</th>
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
            { "data": "sale_added_date", "name": "sale_added_date" },
            { "data": "agent_by", "name": "agent_by" },
            // Adjusted to user.fullName
            { "data": "job_category", "name": "job_category" },
            { "data": "job_title", "name": "job_title" },
            { "data": "office_name", "name": "office_name" },
            { "data": "unit_name", "name": "unit_name" },
            { "data": "postcode", "name": "postcode" },
            { "data": "job_type", "name": "job_type" },
            { "data": "experience", "name": "experience" },
            // { "data": "qualification", "name": "qualification" },
            { "data": "salary", "name": "salary" },
            { "data": "no_of_sent_cv", "name": "no_of_sent_cv" },
            { "data": "sale_notes", "name": "sale_notes" }
        ];

        $(document).ready(function() {
            var start_date = $('#startDate').val();
            var end_date = $('#endDate').val();
            {{--console.log("Data to display:", <?php echo json_encode($sales); ?>); // Log data to console--}}
            $('#sale-open-table').DataTable({
                "processing": true,
                "serverSide": false, // Since data is already fetched from the server
                {{--"data": <?php echo json_encode($sales); ?>, // Pass the fetched data--}}
                "ajax": {
                    "url": "/get-sale_open/" + start_date + "/" + end_date,
                    "type": "GET" // Use GET method for passing parameters
                },
                "columns": columns,
                "error": function(xhr, error, thrown) {
                    console.log("DataTables error:", error, thrown); // Log DataTables error
                }
            });
        });




    </script>







@endsection
