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
        .nav-tabs .nav-link.active {
            background-color: #007bff; /* Change to your desired active tab background color */
            color: #fff; /* Change to your desired active tab text color */
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            padding: 0em 0em;
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
                            <div class="card-body border border-lightblue rounded-lg">
                                @if ($sale->status == 'active')
                                    <a href="javascript:void(0)" class="btn btn-sm greenButton float-right text-white">{{ $sale->status }}</a>
                                @else
                                    <a href="javascript:void(0)" class="btn btn-sm redButton float-right text-white">{{ $sale->status }}</a>
                                @endif
                                <div class="d-flex justify-content-center align-items-center py-3">
                                    <img class="rounded-circle border border-dark mt-4" src="{{ asset('img/avatar/default-avatar.png') }}" alt="no image" height="100" width="100">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Member Since</h6>
                                    <span id="memberSince">{{ $sale->created_at->format('m/d/y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Unit Head Office</h6>
{{--                                    <h5 class="  mr-5" id="userName">--}}
                                        @php
                                            $unit_office=\App\Models\Office::where('id',$sale->head_office)->first();
                                        @endphp
                                        {{$unit_office?$unit_office->name:'N/A'}}
{{--                                    </h5>--}}
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Unit Head Office</h6>
                                    <span id="doublePartner" class="text-dark">
                    @php
                        $head_office=\App\Models\Unit::where('id',$sale->head_office_unit)->first();
                    @endphp
                                        {{$head_office?$head_office->unit_name:'N/A'}}
                 </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Job Type</h6>
                                    <span id="doublePartner" class="text-dark">
                     {{$sale->job_type}}
                 </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Benefits</h6>
                                    <span id="mixedDoublePartner" class="text-black">
                    {{$sale->benefits}}
                 </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Sale Salary</h6>
                                    <span id="mixedDoublePartner" class="text-black">
                    $ {{$sale->salary}}
                 </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Sale Timing</h6>
                                    <span id="mixedDoublePartner" class="text-black">
                    {{$sale->time}}
                 </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Sale Experience</h6>
                                    <span id="mixedDoublePartner" class="text-black">
                    {{$sale->experience}}
                 </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Sale Qualification</h6>
                                    <span id="mixedDoublePartner" class="text-black">
                    {{$sale->qualification}}
                 </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>Sale Postcode</h6>
                                    <span id="mixedDoublePartner" class="text-black">
                    {{$sale->postcode}}
                 </span>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-7 col-6">
                                        <!-- Placeholder for additional content if needed -->
                                    </div>
                                    <div class="col-md-4 col-5 d-flex mr-1">
                                        <!-- Placeholder for additional content if needed -->
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
{{--                                                        <a href="#tab_default_1" data-toggle="tab" id="generalTab"--}}
{{--                                                           class="text-decoration-none text-black"--}}
{{--                                                           data-id="{{$sale->id}}">--}}
{{--                                                            General Notes </a>--}}

                                                        <a class="nav-link active text-black" href="#tab_default_1" data-toggle="tab" id="generalTab" data-id="{{$sale->id}}">
                                                            General Notes
                                                        </a>
                                                    </li>

{{--                                                    <li>--}}
{{--                                                        <a href="#tab_default_4" data-toggle="tab"--}}
{{--                                                           class="nav-link text-black" id="challengeTab"--}}
{{--                                                           data-url="{{ $sale->id }}">--}}
{{--                                                            CV Quality </a>--}}
{{--                                                    </li>--}}
{{--                                                    <li>--}}
{{--                                                        <a href="#tab_default_5" data-toggle="tab"--}}
{{--                                                           class="text-decoration-none text-black" id="rankingTab"--}}
{{--                                                           data-id="{{ $sale->id }}">--}}
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
                                                                    @can('office_note-history')
                                                                    <table class="table table-striped table-borderless" id="office-notes"
                                                                           style="margin: 0px !important; width:100%">
                                                                        <thead>
                                                                        <tr>
                                                                            <th># No.</th>
                                                                            <th>Date</th>
                                                                            <th>Time</th>
                                                                            <th>User Name</th>
                                                                            <th>Notes</th>
                                                                            <th>Note Type</th>
                                                                            <th>Status</th>

                                                                        </tr>
                                                                        </thead>
                                                                        {{--                                                                        <tbody></tbody>--}}
                                                                    </table>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- general tab  --}}
                                                    {{--                                                    @include('users.tables.rankings',['userId'=>$sale->id])--}}

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

            var url='/sale_notes/'+ userId;
            var table = $('#office-notes').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: url,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'date', name: 'date'},
                    {data: 'time', name: 'time'},
                    {data: 'user_name', name: 'user_name'},
                    {data: 'notes', name: 'notes'},
                    {data: 'type', name: 'type'},
                    {data: 'status', name: 'status'},
                ],
                lengthMenu: [10, 25, 50, 100],
                pageLength: 10,
                // dom: '<"d-flex justify-content-between"lfB<t>ip>',


            });
        });


    </script>

@endsection
