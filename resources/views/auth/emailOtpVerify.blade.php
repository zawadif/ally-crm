@extends('layouts.authMaster')

@section('title','OTP')

@section('style')
<link href="{{ asset('css/authPage.css') }}" rel="stylesheet">
@endsection


@section('content')
    <div class="col-lg-12">
        <h5 class="text-white mb-0">Email Verification</h5>
        <small class="text-white" style="letter-spacing: 1px;font-size: 13px;">Enter 4-digit code sent via email.</small>
    </div>
    <div class="col-lg-12">
        @if(session()->has('message'))
        <div class="alert alert-danger">
         {{ session()->get('message') }}
      </div>
       @endif
        <form class="mt-3" id="otpValidateOtpForm">
            @csrf
            <input id="otpType" name="otpType" type="hidden" value="EMAIL">
            <input id="email" name="email" type="hidden" value="{{ $user->email }}">
            <div class="d-flex justify-content-center align-items-center form-group mb-3">
                <div class="input-group px-1">
                    <input type="text" id="otp1" name="otp[]" maxlength="1" class="form-control inputFieldHeight borderLeftNone font12 p-4" placeholder="X">
                </div>
                <div class="input-group  px-1">
                    <input type="text" id="otp2" name="otp[]" maxlength="1" class="form-control inputFieldHeight borderLeftNone font12 p-4" placeholder="X">
                </div>
                <div class="input-group px-1">
                    <input type="text" id="otp3" name="otp[]" maxlength="1" class="form-control inputFieldHeight borderLeftNone font12 p-4" placeholder="X">
                </div>
                <div class="input-group px-1">
                    <input type="text" id="otp4" name="otp[]" maxlength="1" class="form-control inputFieldHeight borderLeftNone font12 p-4" placeholder="X">
                </div>
            </div>
            <div class="invalid-feedback mb-2" id="otpError"></div>
            <input type="submit" class="font12 btn btn-block btn-success btn-lg rounded greenButton" value="Continue">
        </form>
    </div>
    <div class="col-lg-12">
        <form id="resendOTPForm">
            @csrf
            <input id="otpType" name="otpType" type="hidden" value="EMAIL">
            <input id="email" name="email" type="hidden" value="{{ $user->email }}">
            <input type="submit" class="btn btn-link float-right" value="Resend OTP">
        </form>
    </div>
@endsection


@section('script')
<script src="{{ asset('js/authPage.js')}}"></script>
@endsection
