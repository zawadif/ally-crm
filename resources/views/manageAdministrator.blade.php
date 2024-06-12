@extends('layouts.master')

@section('title','Manage Administrator')

@section('style')
<link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content pt-4 px-5">
            <div class="row ml-0" style="width: 100%;border-bottom: 1px solid black;">
                <ul class="nav nav-pills" style="font-size: 14px;">
                    <li class="nav-item"><span class="nav-link px-5 rounded-0 active" href="#tab_1" data-toggle="tab" style="">Admin Users</span></li>
                    <li class="nav-item"><span class="nav-link px-5 rounded-0" href="#tab_2" data-toggle="tab">Admin Roles</span></li>
                </ul>
            </div>
            <div class="tab-content mt-5">
                <div class="tab-pane active table-responsive" id="tab_1">
                    
                </div>
                <div class="tab-pane " id="tab_2">
                    Roles
                </div>
            </div>
        
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('script')
{{-- <script src="{{ asset('js/authPage.js')}}"></script> --}}
@endsection