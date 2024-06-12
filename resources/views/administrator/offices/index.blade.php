@extends('layouts.master')
@section('title','Franchises Management')
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
                        <h1>Franchises Management</h1>
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
                                <h3 class="card-title" style="color: white">Franchises</h3>
                                @can('office_create')
                                    <div style="float: right;">
                                        <a href="{{ route('offices.create') }}" class="btn btn-sm btn-primary greenButton">
                                            <i class="fa fa-user-nurse"></i> Add Franchises
                                        </a>
                                    </div>

                                @endcan

                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table" id="office_table_1">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Head Office Name</th>
                                        <th>Postcode</th>
                                        <th>Type</th>
                                        <th>Phone</th>
                                        <th>Landline</th>
                                        <th>Notes</th>
                                        <th>Status</th>
                                        @canany(['office_edit','office_view','office_note-create'])
                                            <th>Action</th>
                                        @endcanany
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
            { "data":"office_name", "name": "office_name" },
            { "data":"postcode", "name": "postcode" },

            // { "data":"email", "name": "email", "orderable": true },
            { "data":"type", "name": "type", "orderable": false },

            { "data":"phone_number", "name": "phone_number" },
            { "data":"contact_landline", "name": "contact_landline" },
            { "data":"notes", "name": "notes" },
            { "data":"status", "name": "status" },
            { "data":"action", "name": "action" }
        ];

        $(document).ready(function() {
            // $.fn.dataTable.ext.errMode = 'none';

            $('#office_table_1').DataTable({

                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax":"getOffices",
                "order": [],
                "columns": columns
            });

        });
        $(document).on('click', '.notes_history', function (event) {
            var office = $(this).data('office');

            $.ajax({
                url: "{{ route('notesHistory') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    module_key: office,
                    module: "Office"
                },
                success: function(response){
                    //later
                    window.location.href = '/offices';
                },
                error: function(response){
                    var raw_html = '<p>WHOOPS! Something Went Wrong!!</p>';
                    $('#office_notes_history'+office).html(raw_html);
                }
            });
        });

    </script>



@endsection
