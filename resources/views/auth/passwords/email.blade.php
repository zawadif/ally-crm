@extends('layouts.authMaster')

@section('title','Forgot Password')

@section('style')
<link href="{{ asset('css/authPage.css') }}" rel="stylesheet">
@endsection


@section('content')
    <div class="col-lg-12">
        <h5 class="text-white mb-0">Forgot Password?</h5>
        <small class="text-white" style="letter-spacing: 1px;font-size: 13px;">Enter your email address to request new password.</small>
    </div>
    <div class="col-lg-12">
        @if (session('status'))
            <div class="mt-3 alert alert-success text-center">{{ session()->get('status')}}</div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session()->get('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form class="mt-3" action="{{ route('password.email') }}" method="post">
            @csrf
            <div class="form-group mb-3">
                <label class="text-white " for="exampleInputEmail1"><small>Email address</small></label>    
                <div class="input-group ">
                    <div class="input-group-prepend inputFieldHeight borderRightNone">
                      <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                    </div>
                    <input type="email" id="email" name="email"  class="form-control inputFieldHeight borderLeftNone font12" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                </div>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            <input type="submit" class="font12 btn btn-block btn-success btn-lg rounded greenButton" value="Request New Password">
        </form>
    </div>
@endsection


@section('script')
<script src="{{ asset('js/authPage.js')}}"></script>
@endsection
