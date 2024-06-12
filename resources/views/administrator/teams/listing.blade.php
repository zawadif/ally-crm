@extends('layouts.master')
@section('title','Team Management')
@section('style')
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
                        <h1>Team Management</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
        <div class="container-fluid">
        <div class="row">
                <!-- category create form -->
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <div class="card">
                         <div class="card-header"style="background-color: white">
                            <h3 class="card-title" id="categoryTitle">Add New Team Member</h3>
                            </div>
                        <div class="card-body">
                          <form   id="addForm" method="post"  action="#"  enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="fullName" class="form-control" value="{{ old('fullName') }}" required autocomplete="off" id="fullName" placeholder="Enter First Name">
                            </div>

                              <div class="form-group">
                                <label>Role</label>
                                 <select class="form-control"style="width:100%" name="role" required>
                                 @foreach($roles as $p)
                                  <option value="{{ $p->name }}">{{ ucfirst($p->name) }}</option>
                                 @endforeach
                                 </select>
                            </div>
                             <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" value="{{ old('email') }}" required autocomplete="off" id="email" placeholder="Enter Email">
                                 <span class="text-danger" id="emailError"></span>

                             </div>
{{--                              <div class="form-group">--}}
{{--                                  <label>Phone Number</label>--}}
{{--                                  <input type="text" name="phoneNumber" class="form-control" value="{{ old('phoneNumber') }}" required autocomplete="off" id="phoneNumber" placeholder="Enter phoneNumber">--}}
{{--                                  <span class="text-danger" id="phoneNumberError"></span>--}}

{{--                              </div>--}}
                              <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" id="addTeanPassword"
                                           placeholder="Set Password" min="6">
                                    <span toggle="#addTeanPassword" class="fa fa-fw fa-eye field-icon addTeanPassword"
                                          style="position: relative;float: right !important;   top: -23px;  left: -11px;"></span>

                                </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary btn-flat text-white active-nav">Add Team</button>
                                </div>
                                 <div class="col-md-4 mt-2">
                                    <a href="#"  id="clearAll" class="text text-bold text-info">Clear all</a>
                                </div>

                            </div>
                          </form>
                        </div>
                    </div>
                </div>
          <!-- end category create form -->
                  <div class="col-md-9">

                  <div class="card">

                    <div class="card-header" style="background-color: purple">
                      <h3 class="card-title" style="color: white">Team Members</h3>

                    </div>

                       <!-- /.box-header -->
                    <div class="card-body">
                      @include('administrator.teams.inc.userTable')

                    </div>

                      </div>
                  </div>
              </div>
          </div>
        </section>
        </div>

        <!-- /.content -->
{{--    </div>--}}
    <!-- /.content-wrapper -->
    @include('administrator.teams.inc.add')

@endsection

@section('script')
{{--    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

{{--    <script src="{{ asset('js/teams/loginDetail.js') }}"></script>--}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
{{--    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>--}}
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script  src="{{ asset('js/teams/list.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var table = $('#user_sample_1').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('teams/all') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'date', name: 'date'},
                {data: 'time', name: 'time'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'role', name: 'role'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action'},
            ]
        });
    });
</script>
@endsection
