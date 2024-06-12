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
                        <h1>Units Management</h1>
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
                                <h3 class="card-title" style="color: white">Units</h3>
                                <div style="float: right">
                                    @can('unit_create')
                                    <a  href="{{route('units.create')}}"  class="btn btn-sm btn-primary greenButton"><i class="fa fa-plus-circle"></i>  Add Unit </a>
                                    @endcan
                                </div>

                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table" id="unit_table_1">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Unit Name</th>
                                        <th>Unit Email</th>
                                        <th>Head Office Name</th>
                                        <th>Postcode</th>
{{--                                        <th>Type</th>--}}
                                        <th>Phone</th>
                                        <th>Landline</th>
                                        <th>Notes</th>
                                        <th>Status</th>
{{--                                        @canany(['office_edit','office_view','office_note-create'])--}}
                                            <th>Action</th>
{{--                                        @endcanany--}}
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


        // var columns = [
        //     { "data":"date", "name": "date" , "orderable": false},
        //     { "data":"time", "name": "time" , "orderable": false},
        //     { "data":"unit_name", "name": " unit_name" ,"orderable": true , "searchable": true  },
        //     { "data":"contact_email", "name": " contact_email", "orderable": true , "searchable": true  },
        //     { "data":"type", "name": "type","orderable": false },
        //     { "data":"unit_postcode", "name": "unit_postcode", "orderable": true , "searchable": true  },
        //     { "data":"phone_number", "name": "phone_number" , "orderable": true , "searchable": true },
        //     { "data":"contact_landline", "name": "contact_landline" },
        //     { "data":"notes", "name": "notes","orderable": false },
        //     { "data":"status", "name": "status","orderable": false },
        //     { "data":"action", "name": "action" ,"orderable": false}
        // ];

        $(document).ready(function () {
            var table = $('#unit_table_1').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax": "getUnits",
                "columns": [
                    { "data": "date" },
                    { "data": "time" },
                    { "data": "unit_name" },
                    { "data": "contact_email" },
                    { "data": "type" },
                    { "data": "unit_postcode" },
                    { "data": "contact_phone_number" },
                    { "data": "contact_landline" },
                    { "data": "notes" },
                    { "data": "status" },
                    { "data": "action" }
                ]
            });

            // Custom search functionality
            $('#search').on('keyup', function () {
                table.columns([2, 3]).search(this.value).draw(); // Columns index 2 and 3 correspond to 'unit_name' and 'contact_email' respectively
            });
            });
        // var columns = [
        //     { "data":"date", "name": "date" , "orderable": false},
        //     { "data":"time", "name": "time" , "orderable": false},
        //     { "data":"unit_name", "name": "unit_name" ,"orderable": true , "searchable": true  },
        //     { "data":"contact_email", "name": "contact_email", "orderable": true , "searchable": true  },
        //     { "data":"type", "name": "type","orderable": true },
        //     { "data":"unit_postcode", "name": "unit_postcode", "orderable": true , "searchable": true  },
        //     { "data":"contact_phone_number", "name": "contact_phone_number" , "orderable": true , "searchable": true },
        //     { "data":"contact_landline", "name": "contact_landline" },
        //     { "data":"notes", "name": "notes","orderable": false },
        //     { "data":"status", "name": "status","orderable": false },
        //     { "data":"action", "name": "action" ,"orderable": false}
        // ];
        //
        //
        // $(document).ready(function() {
        //     // $.fn.dataTable.ext.errMode = 'none';
        //
        //     $('#unit_table_1').DataTable({
        //
        //         "processing": true,
        //         "serverSide": true,
        //         "responsive": true,
        //         "searching": true,
        //         "ajax":"getUnits",
        //
        //         // "order": [],
        //         "columns": columns
        //     });
        //     $('#search').on('keyup', function () {
        //         table.column(2).search(this.value).draw(); // Column index 2 corresponds to the 'unit_name' column
        //     });
        //
        // });

        $(document).on('click', '.notes_history', function (event) {
            var office = $(this).data('unit');

            $.ajax({
                url: "{{ route('notesHistory') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    module_key: office,
                    module: "Unit"
                },
                success: function(response){
                    $('#unit_notes_history'+office).html(response);
                },
                error: function(response){
                    var raw_html = '<p>WHOOPS! Something Went Wrong!!</p>';
                    $('#unit_notes_history'+office).html(raw_html);
                }
            });
        });

    </script>



@endsection
