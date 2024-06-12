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
                                <h3 class="card-title" style="color: white">Resource Job Nurses</h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table" id="office_table_1">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Job Title</th>
                                        <th>Head Office</th>
                                        <th>Unit</th>
                                        <th>Postcode</th>
                                        <th>Type</th>
                                        <th>Experience</th>
                                        <th>Qualification</th>
                                        <th>Salary</th>
{{--                                        <th>Notes</th>--}}
                                        <th>Cv's Limit</th>
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
        {{--$(document).ready(function() {--}}
        {{--    $('#office_table_1').DataTable({--}}
        {{--        "processing": true,--}}
        {{--        "serverSide": true,--}}
        {{--        "ajax": "{{ route('getOffices') }}", // Use route() to generate URL--}}
        {{--        "order": [[0, "desc"]],--}}
        {{--        "pagingType": "full_numbers",--}}
        {{--        "lengthMenu": [[10, 50, 100, 500], [10, 50, 100, 500]],--}}
        {{--        "columns": [--}}
        {{--            { "data": "id" }, // Replace 'id' with the actual column name for your index--}}
        {{--            { "data": "date" },--}}
        {{--            { "data": "time" },--}}
        {{--            { "data": "office_name" },--}}
        {{--            { "data": "status" },--}}
        {{--            { "data": "postcode" },--}}
        {{--            { "data": "email" },--}}
        {{--            { "data": "phone_number" },--}}
        {{--            { "data": "contact_landline" },--}}
        {{--            { "data": "type" },--}}
        {{--            { "data": "notes" }--}}
        {{--        ]--}}
        {{--    });--}}

        {{--    // table.destroy();--}}

        {{--});--}}

        var columns = [
            // { "data":"id", "name": "id" },
            { "data":"date", "name": "date" },
            { "data":"time", "name": "time" },
            { "data":"office_name", "name": "Head Office Name" },
            { "data":"postcode", "name": "postcode" },

            // { "data":"email", "name": "email", "orderable": true },
            { "data":"phone_number", "name": "number" },
            { "data":"contact_landline", "name": "landline" },
            { "data":"type", "name": "type", "orderable": false },
            // { "data":"notes", "name": "notes" },
            { "data":"status", "name": "status" },
            { "data":"action", "name": "action" }
        ];

        $(document).ready(function() {
            // $.fn.dataTable.ext.errMode = 'none';

            $('#office_table_1').DataTable({
                "aoColumnDefs": [{"bSortable": false, "aTargets": [0,10]}],
                "bProcessing": true,
                "bServerSide": true,
                "aaSorting": [[0, "desc"]],
                // "sPaginationType": "full_numbers",
                "sAjaxSource": "{{ url('getNursingJob') }}",
                "aLengthMenu": [[10, 50, 100, 500], [10, 50, 100, 500]]
            });

        });

    </script>



@endsection
