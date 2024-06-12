@extends('layouts.webportalMaster')
@section('title','Web Portal My Matches')

@section('style')
     <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" />
     <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css"
         rel="stylesheet">
     <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
     <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
        <link href="{{ asset('css/matchTable.css') }}" rel="stylesheet">
@endsection

@section('content')
 <div class="content-wrapper" style="background-color: #f8f9fa;">
    <section class="content pt-4 px-lg-5 px-md-2 px-sm-2 px-xs-2">
        <div class="row">
            <div class="col-4">
                <b style="font-size:medium">My Matches </b>
            </div>
            <div class="col-2"></div>
             <div class="col-6 float-right">

                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-4">
                             <div class="parent">
                                <select name="select" id="select" class="form-control form-control-sm selectpiker" style="width:100%"  onchange="changeTag()">
                                        <option value="">status:All</option>
                                        <option value="Played">Played</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                             </div>

                    </div>
                    <div class="col-4">
                     <input class="form-control form-control-sm" type="search" id="matchsearch" placeholder="Search" name="search"
                             style="width: 100%;padding-left: 10px;border: 1px solid;border-radius: 2px;">
                    </div>

                </div>
             </div>
        </div>
        {{-- <div class="row"> --}}
            <section class="content px-1 mt-2">
                <!-- Default box -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="card-header" style="background: #f8f9fa;">
                            <div class="row">
                                 <div class="col-lg-10 col-md-6 d-flex justify-content-start ">
                                    <span class="pl-2"><em>Match List</em></span>
                                </div>
                                <div class="col-lg-2 col-md-6" style="float: right !important">
                                    <span id="matches_info" class="pl-2 matches_table_info"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xlg-12 col-lg-12 table-responsive">
                                <table class="table table-striped table-borderless" id="webportalMatchesTable"
                                    style="margin: 0px !important; width:100%">
                                    <thead>
                                        <tr>
                                            <th># No.</th>
                                            <th>Opponent</th>
                                            <th>Time</th>
                                            <th>Winner Team</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </section>
        {{-- </div> --}}

    </section>
</div>
@endsection

@section('script')
    <script src="{{ asset('js/matchTable.js') }}"></script>
@endsection
