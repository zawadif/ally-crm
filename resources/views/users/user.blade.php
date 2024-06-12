 @extends('layouts.master')
 @section('title', 'Profile')

 @section('style')
     <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" />
     <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css"
         rel="stylesheet">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>

     <link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
     <link href="{{ asset('css/user.css') }}" rel="stylesheet">
 @endsection
 @section('content')
     <!-- Content Wrapper. Contains page content -->
     <div class="content-wrapper">

         <!-- Content Header (Page header) -->
         <section class="content-header">
             <div class="container-fluid">
                 <div class="row d-flex justify-content-between">
                     <div class="col-xlg-6 col-lg-6 col-md-4 col-sm-12 col-12 d-flex justify-content-start pt-1">
                         <h5 class="pt-2">App Users</h5>
                     </div>
                     <div class="col-xlg-6 col-lg-6  col-md-8 col-sm-12 col-12 d-flex justify-content-end pt-1">
                         <div class="row">
                             <div class="col-xlg-6 col-lg-6 col-md-8 col-sm-12 col-12 d-flex justify-content-end">
                                 <label for="" class="pt-2"><small><b>Show Users:</b></small></label>
                                 <select class="form-select border-0 selectLook" name="userTag" id="userTag"
                                     data-size="5" onchange="changeTag()"
                                     style="max-width:6rem;
                                     ">
                                     <option value="0">All Users</option>
                                     <option value="1">Paid Users</option>
                                     <option value="2">Free Users</option>
                                 </select>

                             </div>
                             <div class="col-xlg-6 col-lg-6 col-md-4 col-sm-12 col-12  pt-2 d-flex justify-content-end">
                                 <input class="" type="text" id="usersearch" placeholder="Search" name="usersearch"
                                     style="width: 100%;padding-left: 12px;border: 1px solid;border-radius: 5px;">
                             </div>
                         </div>
                     </div>
                 </div>

             </div>
         </section>

         {{-- Proposal Table --}}
         @include('users.tables.userTable')

         <!-- /.content -->
     </div>
     @include('users.modal.deleteUserModal')
     @include('users.modal.rightSideBar')
      @include('users.modal.createChatModal')
 @endsection

 @section('script')

     <script src="{{ asset('js/activeUser.js') }}"></script>
     <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
 @endsection
