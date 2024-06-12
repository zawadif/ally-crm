<section class="content px-1">
    <!-- Default box -->
    <div class="row p-1">
        <div class="col-lg-8 col-md-6">
            <h5 class="pl-2">Matches</h5>
        </div>
        <div class="col-lg-4 col-md-6 d-flex justify-content-start">
            <div class="d-flex p-1">Filters
                <i class="fas fa-light fa-filter pt-1 filter"></i></div>
            <input class="" type="search" id="matchSearch" placeholder="Search" name="matchSearch"
                style="width: 100%;padding-left: 12px;border: 1px solid;border-radius: 5px;">
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="card-header" style="background-color: purple">
                <div class="row p-1">
                    <div class="col-lg-10 col-md-6 d-flex justify-content-start ">
                        <span class="pl-2 text-white">{{ $user->fullName }}'s Matches</span>
                    </div>
                    <div class="col-lg-2 col-md-6 text-white" style="float: right !important">
                        <span id="matches_info" class="pl-2 matches_table_info"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xlg-12 col-lg-12 table-responsive">
                    <table class="table table-striped table-borderless" id="matches-table"
                        style="margin: 0px !important; width:100%">
                        <thead>
                            <tr>
                                <th># No.</th>
                                <th>Login Time</th>
                                <th> Logout Time</th>
{{--                                <th></th>--}}
                                <th>Status</th>
{{--                                <th>Action</th>--}}
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
@section('script')
{{--    <script src="{{ asset('js/teams/loginDetail.js') }}"></script>--}}
@endsection
