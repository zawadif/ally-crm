@extends('layouts.master')
@section('style')
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
@endsection
@section('content')

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
{{--        <div class="page-header page-header-dark has-cover" style="border: 1px solid #ddd; border-bottom: 0;">--}}
        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-inline">
                <div class="page-title">
                    <h5>
                        <i class="icon-arrow-left52 mr-2"></i>
                        <span class="font-weight-semibold">Roles</span> - All
                    </h5>
                </div>
            </div>

{{--            <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">--}}
{{--                <div class="d-flex">--}}
{{--                    <div class="breadcrumb">--}}
{{--                        <a href="#" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Roles</a>--}}
{{--                        <span class="breadcrumb-item active">All</span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Default ordering -->
            <div class="card border-top-teal-400 border-top-3">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Role Management</h5>
                    <div style="float:right;">
                    @can('role_create')

                    <a href="{{ route('roles.create') }}" class="btn bg-teal greenButton"><i
                                class="fa-critical-role"></i> Role</a>
                    @endcan
                    @can('role_assign-role')
                            <a href="#" data-controls-modal="#assign_role"
                               data-backdrop="static"
                               data-keyboard="false" data-toggle="modal"
                               data-target="#assign_role"
                               class="btn bg-dark legitRipple"
                            >
                                <i class="fas fa-user-tag"></i> Assign Role
                            </a>
                    @endcan
                    </div>
                </div>

                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <div class="card-body">
                <table class="table table-bordered table-striped" id="myTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        @canany(['role_view','role_edit','role_delete'])
                        <th >Action</th>
                        @endcanany
                    </tr>
                    </thead>
                </table>
                </div>
{{--                {!! $roles->render() !!}--}}
            </div>
            <!-- /default ordering -->

        @can('role_assign-role')
            <!-- Assign Role -->
                <div id="assign_role" class="modal fade">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-teal">
                                <h5 class="modal-title text-white">Assign Role to Multiple Users</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('/assign-role-to-users') }}" method="post" enctype="multipart/form-data">
                                    @csrf()
                                    <div class="form-group">
                                        <label for="role" class="font-weight-bold">Role Name</label>
                                        <select name="role" id="role" class="form-control form-control-select2" required>
                                            <option value="">Select Role</option>
                                            @foreach($all_roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" style="border-bottom: 1px solid #DDDDDD;">
                                        <label for="users" class="font-weight-bold">Select Users</label>
                                        <select data-placeholder="Select a User..." class="form-control " name="users[]" id="users" data-fouc required>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->fullName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success btn-teal">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- /Assign Role -->
        @endcan

        </div>
        <!-- /content area -->

@endsection
@section('script')
            <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{--            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


            <script>
                $(document).ready(function() {
                    // Initialize Select2 for the multiple select element
                    // $('#users').select2({
                    //     placeholder: 'Select Users',
                    //     // Add other options/configurations here as needed
                    // });
                });

                $(document).ready(function() {
                    var url="/roles-all";
                    $('#myTable').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": url,
                        "columns": [
                            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                            { "data": "name" },
                            { "data": "action" }
                            // { "data": "email" },
                            // { "data": "created_at" }
                        ]
                    });
                });
            </script>

@endsection
