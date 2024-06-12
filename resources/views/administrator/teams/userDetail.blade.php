@extends('layouts.master')
@section('title', 'User')

@section('style')
{{--    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css"
          rel="stylesheet">
{{--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>--}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('css/user.css') }}" rel="stylesheet">

    <style>
        .modal {
            display: none; /* Hide the modal by default */
        }

        #loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .dataTables_length{
            float: left !important;
        }
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

        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row min-vh-500">

                    <div class="col-lg-4 col-md-4">
                        <div class="card mb-4" style="min-height: 44rem">

                            <div class="card-body  border border-lightblue rounded-lg">


                                @if ($user->status =='ACTIVE')
                                    <a href="javascript:void(0)"
                                       class="btn btn-sm greenButton float-right text-white">{{ $user->status }}</a>
                                @else
                                    <a href="javascript:void(0)"
                                       class="btn btn-sm btn-danger float-right text-white ">{{ $user->status }}</a>
                                @endif
                                <div class="d-flex justify-content-center align-items-center py-3">
                                    <img class="rounded-circle border border-dark mt-4"
                                         src="{{ $user->avatar ? Storage::disk('s3')->url($user->avatar) : asset('img/avatar/default-avatar.png') }}"
                                         alt=" no image" height="100" width="100">
                                </div>
                                <h5 class="text-center font-weight-bold mr-5" id="userName">{{ $user->fullName }}</h5>
                                <div class="d-flex justify-content-between">
                                    <h6>Member Since</h6>
                                    <span id="memberSince">{{ $user->createdAt->format('m/d/y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>User ID</h6>
                                    <span id="userId">{{ $user->id }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>User Email</h6>
                                    <span id="doublePartner" class="text-dark">
                                         {{$user->email}}
                                     </span>
                                </div>
{{--                                <div class="d-flex justify-content-between">--}}
{{--                                    <h6>User Number</h6>--}}
{{--                                    <span id="mixedDoublePartner" class="text-black">--}}
{{--                                        {{$user->phoneNumber}}--}}
{{--                                     </span>--}}
{{--                                </div>--}}
                                <div class="d-flex justify-content-between">
                                    <h6>Password</h6>
                                    <span id="reset" class="text-black">
                                         <p><a href="javascript:void(0)" class="pe-auto text-dark" id="passworReset">Reset Password</a></p>
                                     </span>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-7 col-6 ">
                                        @can('user_edit')
                                        <button type="button" class="btn btn-block text-center rounded  pb-1 text-white greenButton"  id="testBtn" data-id="{{$user->id}}">
                                            <span>Edit Profile</span>
                                             </button>
                                        @endcan

                                    </div>

                                    <div class="col-md-4 col-5 d-flex mr-1">
                                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split greenButton" id="dropdownMenuReference" data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuReference">
                                            @can('user_enable-disable')

                                            @if($user->status=="ACTIVE")
                                            <li><a href="#" class="dropdown-item" id="statusChange" data-id="{{$user->id}}" >Status Block</a></li>

                                                @else
                                                <li><a href="#" class="dropdown-item" id="statusChange" data-id="{{$user->id}}" >Status Active</a></li>

                                            @endif
                                            @endcan
{{--                                            @can('user_delete')--}}
                                                <li><a class="dropdown-item btn btn-danger" id="userDelete" data-id="{{$user->id}}" href="#">Delete User</a></li>
{{--                                                @endcan--}}

                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8  col-md-8">
                        <div class="card mb-4 " style="min-height: 44rem; max-height: 44rem;">
                            <div class="card-body border border-lightblue rounded-lg overflow-auto scrollingWrapper">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="tabbable-panel">
                                            <div class="tabbable-line">
                                                <ul class="nav nav-tabs">
                                                    <li class="active">
                                                        <a href="#tab_default_1" data-toggle="tab" id="generalTab"
                                                           class="text-decoration-none text-black btn btn-info"
                                                            data-id="{{$user->id}}">
                                                            General History</a>
                                                    </li>

{{--                                                    <li>--}}
{{--                                                        <a href="#tab_default_4" data-toggle="tab"--}}
{{--                                                           class="text-decoration-none text-black  btn btn-info" id="challengeTab"--}}
{{--                                                           data-url="{{ $user->id }}">--}}
{{--                                                            CV Quality </a>--}}
{{--                                                    </li>--}}
{{--                                                    <li>--}}
{{--                                                        <a href="#tab_default_5" data-toggle="tab"--}}
{{--                                                           class="text-decoration-none text-black btn btn-info" id="rankingTab"--}}
{{--                                                           data-id="{{ $user->id }}">--}}
{{--                                                            CRM </a>--}}
{{--                                                    </li>--}}

                                                </ul>

                                                <hr class="mt-0" style="color: #0000007A;width:100%">
                                                <div class="tab-content">
                                                    <div class="card">
                                                        <div class="card-body p-0">
                                                            <div class="card-header" style="background-color: purple">
                                                                <div class="row p-1">
                                                                    <div class="col-lg-2 col-md-6 text-white" style="float: right !important">
                                                                        <span id="rankings_info" class="pl-2 rankings_table_info"></span>
                                                                    </div>
                                                                </div>
                                                            </div><br>
                                                            <div class="row">
                                                                <div class="col-xlg-12 col-lg-12 table-responsive">
                                                                    <table class="table table-striped table-borderless" id="rankings-table"
                                                                           style="margin: 0px !important; width:100%">
                                                                        <thead>
                                                                        <tr>
                                                                            <th># No.</th>
                                                                            <th>Start Time</th>
                                                                            <th>End Time</th>

                                                                        </tr>
                                                                        </thead>
{{--                                                                        <tbody></tbody>--}}
                                                                    </table>
                                                                    <div class="table-responsive">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- general tab  --}}
{{--                                                    @include('users.tables.rankings',['userId'=>$user->id])--}}

                                                    <div class="tab-pane" id="tab_default_1">
{{--                                                        @include('users.tables.matches')--}}
                                                    </div>
                                                    {{-- matches tab  --}}
                                                    <div class="tab-pane" id="tab_default_2">
{{--                                                        @include('users.tables.matches')--}}
                                                    </div>

                                                    {{-- proposals tab  --}}
                                                    <div class="tab-pane" id="tab_default_3">
{{--                                                        @include('users.tables.proposals')--}}
                                                    </div>

                                                    {{-- challenges tab  --}}
                                                    <div class="tab-pane" id="tab_default_4">
{{--                                                        @include('users.tables.challenges')--}}
                                                    </div>

                                                    {{-- rankings tab  --}}
                                                    <div class="tab-pane" id="tab_default_5">
{{--                                                        @include('users.tables.rankings')--}}
                                                    </div>

                                                    {{-- buying history tab  --}}
                                                    <div class="tab-pane" id="tab_default_6">
{{--                                                        @include('users.tables.buyingHistory')--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--            @include('users.chat.chat')--}}
        </section>

        @include('users.modal.editProfile')
        @include('users.modal.resetPasswordModal')
        @include('users.modal.blockUserModal')




        <!-- /.Main content -->
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="{{ asset('js/teams/loginDetail.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#generalTab').click();
            var userId = $('#generalTab').data('id'); // Access userId passed to this view

            var url='/users/activity/'+ userId;
            var table = $('#rankings-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: url,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'start_time', name: 'start_time'},
                    {data: 'end_time', name: 'end_time'},
                ],
                lengthMenu: [10, 25, 50, 100], // Define the options for the page length menu
                pageLength: 10, // Set the default page length
                // dom: '<"d-flex justify-content-between"lf<t>ip>',

            });
        });

        $(document).ready(function() {
            $('#statusChange').on('click', function(event) {
                event.preventDefault(); // Prevent the default behavior of the anchor tag

                // Open Swal alert
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to change the status?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX POST request
                        var id=$(this).data('id');
                        var url='/users'+'/'+id+'/updateStatus';
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: url, // Replace with your Laravel route URL
                            type: 'POST',
                            data: {
                                id: $('#statusChange').data('id'),
                                // You can send more data if needed
                            },
                            success: function(response) {
                                // Handle successful response
                                console.log('Status updated successfully:', response);
                                Swal.fire('Success', 'Status updated!', 'success');
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                // Handle errors
                                console.error('Error:', error);
                                Swal.fire('Error', 'Failed to update status', 'error');
                            }
                        });
                    }
                });
            });
            $('#userDelete').on('click', function(event) {
                event.preventDefault(); // Prevent the default behavior of the anchor tag

                // Open Swal alert
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this user?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX POST request
                        var id=$(this).data('id');
                        var url='/users'+'/'+id+'/delete';
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: url, // Replace with your Laravel route URL
                            type: 'POST',
                            data: {
                                id: $('#statusChange').data('id'),
                                // You can send more data if needed
                            },
                            success: function(response) {
                                // Handle successful response
                                console.log('Status updated successfully:', response);
                                Swal.fire('Success', 'Status updated!', 'success');
                                window.location.href = '/users';
                            },
                            error: function(xhr, status, error) {
                                // Handle errors
                                console.error('Error:', error);
                                Swal.fire('Error', 'Failed to update status', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

@endsection
