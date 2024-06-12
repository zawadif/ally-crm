@extends('layouts.master')
@section('title', 'Home')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">

                <div class="row mb-2">
                    <div class="col-lg-3">
                        <h4>App Users</h4>
                    </div>
                    <div class="col-lg-6">
                        <select class="float-right p-1 month-select" id="status" name="status" onchange="getStatusValue();"
                            style="border: none">
                            <option>All Users</option>
                            <option>Paid Users</option>
                            <option>Free Users</option>
                        </select>
                        <h4 class="float-right">Show Users:</h4>
                    </div>
                    <div class="col-lg-3">
                        <input class="float-right" type="search" id="gsearch" placeholder="Search" name="gsearch"
                            style="width: 70%;padding-left: 12px;border: 1px solid;border-radius: 5px;">
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content px-4">

            <!-- Default box -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table  " id="users-table" style="margin: 0px !important">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('script')
    <script>
        $(function() {
            $('#users-table').DataTable({
                dom: 'Blfrtip',
                searching: false,
                paging: true,
                info: false,
                lengthChange: false,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{!! route('datatables.data') !!}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'fullName',
                        name: 'fulName'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'createdAt',
                        name: 'created_at'
                    },
                    {
                        data: 'updatedAt',
                        name: 'updated_at'
                    },
                    {
                        data: null,
                        'action': '<button>View</button>'
                    }
                ]
            });
            $('#users-table tbody').on('click', 'button', function() {
                alert("Test");
            })
        });
    </script>
@endsection
