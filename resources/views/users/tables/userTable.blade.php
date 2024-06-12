<section class="content px-4">
    <!-- Default box -->
    <div class="card">
        <div class="row">
            <div class="col-xlg-12 col-lg-12 table-responsive">
                <table class="table table-striped table-borderless" id="users-table"
                    style="margin: 0px !important; width:100%">
                    <thead>
                        <tr>
                            <th># No.</th>
                            <th>Full Name</th>
                            <th>MemberShip</th>
                            <th>Contact #</th>
                            <th>Email</th>
                            <th>Member Since</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>

    </div>

    <!-- /.card -->
</section>

@section('script')
    <script src="{{ asset('js/activeUser.js') }}"></script>
@endsection
