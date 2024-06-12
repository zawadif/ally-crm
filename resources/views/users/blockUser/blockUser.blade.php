 @extends('layouts.master')
 @section('title', 'User')

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
                     <div class="col-xlg-6 col-lg-6 col-md-4 col-sm-6 col-6 d-flex justify-content-start pt-1">
                         <h5 class="pt-2">Blocked Users</h5>
                     </div>
                     <div class="col-xlg-3 col-lg-3 col-md-8 col-sm-8 col-6 d-flex justify-content-end pt-1">
                         <input class="" type="search" id="usearch" placeholder="Search" name="usersearch"
                             style="width: 70%;padding-left: 12px;border: 1px solid;border-radius: 5px;">

                     </div>
                 </div>

             </div>
         </section>

         {{-- Proposal Table --}}
         @include('users.blockUser.table.blockUserTable')

         <!-- /.content -->
     </div>
     @include('users.modal.unblockUserModal')
     @include('users.modal.rightSideBar')

 @endsection

 @section('script')
     <script src="{{ asset('js/activeUser.js') }}"></script>
     <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
 @endsection
