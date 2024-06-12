@extends('layouts.authMaster')

@section('title','Sign Up')

@section('style')
<link href="{{ asset('css/authPage.css') }}" rel="stylesheet">
<style>
    .half{
        padding-top: 260px !important;
        padding-bottom: 30px !important
    }
</style>
@endsection


@section('content')
@php 
session()->forget('url.intended');
@endphp
    <div class="col-lg-12">
        <h5 class="text-white mb-0">Sign Up </h5>
        <small class="text-white" style="letter-spacing: 1px;font-size: 13px;">Welcome to Tennis Fights.</small>
    </div>
    <div class="col-lg-12">
        {{ session()->get('ladderSession') }}
        @if(session()->has('message'))
        <div class="alert alert-danger">
         {{ session()->get('message') }}
      </div>
       @endif
        <form class="mt-3" action="{{route('register')}}" method="post">
            @csrf
            <div class="form-group mb-1">
                <label class="text-white " for="exampleInputEmail1"><small>Email address</small></label>
                <div class="input-group ">
                    <div class="input-group-prepend inputFieldHeight borderRightNone">
                      <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                    </div>
                    @if($receiverEmail == null || $receiverEmail == '')
                        <input type="email" id="email" name="email" class="form-control inputFieldHeight borderLeftNone font12" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" >
                    @else
                        <input type="email" id="email" name="email" class="form-control inputFieldHeight borderLeftNone font12" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" value="{{$receiverEmail}}" readonly>
                    @endif
                  </div>
            </div>
            @if ($errors->has('email'))
                <span class="invalid-feedback" style="display: block;" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
            <div class="form-group mb-3">
                <label class="text-white" for="exampleInputPassword1"><small>Password</small></label>
                <div class="input-group" id="show_hide_password">
                    <div class="input-group-prepend inputFieldHeight borderRightNone">
                      <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
                    </div>
                    <input type="password" id="password" name="password" class="form-control inputFieldHeight borderLeftNone borderRightNone font12" id="exampleInputPassword1" placeholder="Password">
                    <div class="input-group-append inputFieldHeight borderLeftNone">
                        <span class="input-group-text bg-white"><a href="javascript:void(0)"><i id="passwordEye" class="bi bi-eye-slash"></i></a></span>
                    </div>
                  </div>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group mb-3">
                <label class="text-white" for="exampleInputPassword1"><small>Confirm Password</small></label>
                <div class="input-group" id="show_hide_confirmPassword">
                    <div class="input-group-prepend inputFieldHeight borderRightNone">
                      <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
                    </div>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control inputFieldHeight borderLeftNone borderRightNone font12" id="exampleInputPassword1" placeholder="Password">
                    <div class="input-group-append inputFieldHeight borderLeftNone">
                        <span class="input-group-text bg-white"><a href="javascript:void(0)"><i id="confirmPasswordEye" class="bi bi-eye-slash"></i></a></span>
                    </div>
                  </div>
                @if ($errors->has('confirmPassword'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('confirmPassword') }}</strong>
                    </span>
                @endif
            </div>
            <input type="submit" class="font12 btn btn-block btn-success btn-lg rounded greenButton" value="Sign UP">
        </form>
    </div>
@endsection


@section('script')
<script src="{{ asset('js/authPage.js')}}"></script>
@endsection
