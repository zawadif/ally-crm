@extends('layouts.master')

@section('title','Profile')

@section('style')
<link href="{{ asset('css/profileDetail.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content pt-4 px-lg-5 px-md-2 px-sm-2 px-xs-2">
            <div class="row">
                <div class="col-lg-4 pt-3">
                    <div class="card shadow-none blackBorder height100">
                        <div class="card-body pt-4">
                            <div class="row m-0 d-flex justify-content-center">
                                    @if ($user->avatar)
                                        <img src="{{Storage::disk('s3')->url($user->avatar)}}" alt="User Image" style="height: 100px !important;width: 100px !important;border-radius: 50%;">
                                    @else
                                        <img src="{{asset('img/avatar/default-avatar.png')}}" alt="User Image" style="height: 100px !important;width: 100px !important;border-radius: 50%;">
                                    @endif
                            </div>
                            <div class="row m-0 mt-2 d-flex justify-content-center">
                                <p><b>{{ $user->fullName }}</b></p>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="fontSize13 mb-2">Member Since</p>
                                </div>
                                <div class="col-6">
                                    <p class="float-right fontSize13 mb-2">{{ \Carbon\Carbon::parse($user->joinDate)->setTimezone(Session::get('timeZone'))->format('d/m/Y')}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="fontSize13 mb-2">User ID</p>
                                </div>
                                <div class="col-6">
                                    <p class="float-right fontSize13 mb-2">{{"000".$user->id}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="fontSize13 mb-2">Admin Role</p>
                                </div>
                                <div class="col-6">
                                    @role('admin')
                                        <p class="float-right fontSize13 mb-2">{{ $user->roles->first()->name}}</p>
                                    @else
                                    <p class="float-right fontSize13 mb-2">{{ $user->roles->skip(1)->first()->name}}</p>
                                    @endrole
                                </div>
                            </div>
                            <div class="row m-0 mt-5 mx-5">
                                <a href="javascript:void(0)" onclick="editUserProfile({{$user->id}})" class="btn btn-block btn-success greenButton" >Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 pt-3">
                    <div class="card shadow-none blackBorder height100">
                        <div class="card-header p-0 pt-2 white">
                            <ul class="nav nav-pills" style="font-size: 14px;">
                                <li class="nav-item"><a class="nav-link px-5 rounded-0 active" href="#tab_1" data-toggle="tab" style="">Profile Details</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <p class="fontSize13 mb-2">Phone#</p>
                                </div>
                                <div class="col-9">
                                    <p class="fontSize13 mb-2">{{ $user->getUserDetail->phoneNumber }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <p class="fontSize13 mb-2">Email</p>
                                </div>
                                <div class="col-9">
                                    <p class="fontSize13 mb-2">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <p class="fontSize13 mb-2">Gender</p>
                                </div>
                                <div class="col-9">
                                    <p class="fontSize13 mb-2">{{ $user->getUserDetail->gender }}</p>
                                </div>
                            </div>
                            <p class="lineText">Address</p>
                            <div class="row">
                                <div class="col-3">
                                    <p class="fontSize13 mb-2">Address</p>
                                </div>
                                <div class="col-9">
                                    <p class="fontSize13 mb-2">{{ $user->getUserDetail->completeAddress }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <p class="fontSize13 mb-2">Postal Code</p>
                                </div>
                                <div class="col-9">
                                    <p class="fontSize13 mb-2">{{ $user->getUserDetail->postalCode }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <p class="fontSize13 mb-2">State</p>
                                </div>
                                <div class="col-9">
                                    <p class="fontSize13 mb-2">{{ $user->getUserDetail->state }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <p class="fontSize13 mb-2">Country</p>
                                </div>
                                <div class="col-9">
                                        <p class="fontSize13 mb-2">{{$user->getUserDetail->country}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('modals.updateProfile')

@endsection


@section('script')
<script src="{{ asset('js/profileDetail.js')}}"></script>
@endsection
