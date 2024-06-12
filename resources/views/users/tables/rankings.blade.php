<style>
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
<section class="content px-1">
    <!-- Default box -->

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
                        <tbody></tbody>
                    </table>
                    <div class="table-responsive">
{{--                        {!! $dataTable->table(['class' => 'table table-striped ', 'id' => 'teams-table']) !!}--}}

                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- /.card -->
</section>

@section('script')
    <script>
    $(document).ready(function() {
    var userId = '{{ $userId }}'; // Access userId passed to this view
     var url='/users/activity'+'/'+userId;
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
    </script>
{{--    <script src="{{ asset('js/userTable.js') }}"></script>--}}
@endsection
