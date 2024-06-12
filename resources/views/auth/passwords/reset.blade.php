@extends('layouts.authMaster')

@section('title','Create New Password')

@section('style')
<link href="{{ asset('css/authPage.css') }}" rel="stylesheet">
@endsection


@section('content')
    <div class="col-lg-12">
        <h5 class="text-white mb-0">Create New Password</h5>
        <small class="text-white" style="letter-spacing: 0.5px;font-size: 13px;">Create new password that contain at least 8 character,
        uppercase, lowercase, special character and at least one digit.</small>
    </div>
    <div class="col-lg-12">
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session()->get('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form class="mt-3" action="{{route('password.update')}}" method="post">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <input id="email" type="hidden" class="form-control" name="email" value="{{  request()->email }}" required  autocomplete="email" readonly autofocus>
            <div class="form-group mb-3">
                <label class="text-white" for="exampleInputPassword1"><small>Password</small></label>
                <div class="input-group" id="showHidePassword">
                    <div class="input-group-prepend inputFieldHeight borderRightNone">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
                    </div>
                    <input type="password" id="password" name="password" class="form-control inputFieldHeight borderLeftNone borderRightNone font12" id="exampleInputPassword1" placeholder="Password">
                    <div class="input-group-append inputFieldHeight borderLeftNone">
                        <span class="input-group-text bg-white"><a href="javascript:void(0)"><i id="passwordEye" class="bi bi-eye-slash"></i></a></span>
                    </div>
                </div>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
                @if ($errors->has('password'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group mb-3">
                <label class="text-white" for="exampleInputPassword1"><small>Repeat Password</small></label>
                <div class="input-group" id="showHideRepeatPassword">
                    <div class="input-group-prepend inputFieldHeight borderRightNone">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
                    </div>
                    <input type="password" id="password-confirm" name="password_confirmation" class="form-control inputFieldHeight borderLeftNone borderRightNone font12" id="exampleInputPassword1" placeholder="Password">
                    <div class="input-group-append inputFieldHeight borderLeftNone">
                        <span class="input-group-text bg-white"><a href="javascript:void(0)"><i id="repeatPasswordEye" class="bi bi-eye-slash"></i></a></span>
                    </div>
                </div>
            </div>
            <input type="submit" class="font12 btn btn-block btn-success btn-lg rounded greenButton" value="Create Password">
        </form>
    </div>
@endsection


@section('script')
<script src="{{ asset('js/authPage.js')}}"></script>
@endsection
