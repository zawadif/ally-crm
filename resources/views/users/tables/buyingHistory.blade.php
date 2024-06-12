<section class="content px-1">
    <!-- Default box -->
    <div class="row p-1">
        <div class="col-lg-8 col-md-6">
            <h5 class="pl-2">Buying History</h5>
        </div>
        <div class="col-lg-4 col-md-6 d-flex justify-content-start">
            <input class="" type="search" id="buyingHistorySearch" placeholder="Search" name="historySearch"
                style="width: 100%;padding-left: 12px;border: 1px solid;border-radius: 5px;">
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="card-header" style="background-color: purple">
                <div class="row p-1">
                    <div class="col-lg-10 col-md-6 d-flex justify-content-start ">
                        <span class="pl-2 text-white">{{ $user->fullName }}'s Buying History</span>
                    </div>
                    <div class="col-lg-2 col-md-6 text-white" style="float: right !important">
                        <span id="history_info" class="pl-2 history_table_info"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xlg-12 col-lg-12 table-responsive">
                    <table class="table table-striped table-borderless" id="history-table"
                        style="margin: 0px !important; width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Amount Player</th>
                                <th>Partner</th>
                                <th>Season</th>
                                <th>Ladder</th>
                                <th>Buying Date</th>
                                <th>Amount</th>
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
    <script src="{{ asset('js/userTable.js') }}"></script>
@endsection
