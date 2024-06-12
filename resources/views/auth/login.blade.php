{{--@extends('layouts.authMaster')--}}

{{--@section('title', 'Login')--}}

{{--@section('style')--}}
{{--    <link href="{{ asset('css/authPage.css') }}" rel="stylesheet">--}}
{{--@endsection--}}


{{--@section('content')--}}
{{--    <div class="col-lg-12">--}}
{{--        <h5 class="text-white mb-0">Sign in </h5>--}}

{{--            <small class="text-white" style="letter-spacing: 1px;font-size: 13px;">Welcome to RecruitmentAlly Dashboard.</small>--}}

{{--    </div>--}}
{{--    <div class="col-lg-12">--}}
{{--        @if (session()->has('message'))--}}
{{--            <div class="alert alert-danger">--}}
{{--                {{ session()->get('message') }}--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        <form class="mt-3" action="{{ route('login') }}" method="post">--}}
{{--            @csrf--}}
{{--            <input type="hidden" name="time" id="timeZone">--}}
{{--            <div class="form-group mb-1">--}}
{{--                <label class="text-white " for="exampleInputEmail1"><small>Email address</small></label>--}}
{{--                <div class="input-group ">--}}
{{--                    <div class="input-group-prepend inputFieldHeight borderRightNone">--}}
{{--                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>--}}
{{--                    </div>--}}
{{--                    <input type="email" id="email" name="email"--}}
{{--                        class="form-control inputFieldHeight borderLeftNone font12" id="exampleInputEmail1"--}}
{{--                        aria-describedby="emailHelp" placeholder="Enter email">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <input id="type" name="type" type="hidden" value="{{ $type }}">--}}
{{--            @if ($errors->has('email'))--}}
{{--                <span class="invalid-feedback" style="display: block;" role="alert">--}}
{{--                    <strong>{{ $errors->first('email') }}</strong>--}}
{{--                </span>--}}
{{--            @endif--}}
{{--            <div class="form-group mb-3">--}}
{{--                <label class="text-white" for="exampleInputPassword1"><small>Password</small></label>--}}
{{--                <div class="input-group" id="show_hide_password">--}}
{{--                    <div class="input-group-prepend inputFieldHeight borderRightNone">--}}
{{--                        <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>--}}
{{--                    </div>--}}
{{--                    <input type="password" id="password" name="password"--}}
{{--                        class="form-control inputFieldHeight borderLeftNone borderRightNone font12"--}}
{{--                        id="exampleInputPassword1" placeholder="Password">--}}
{{--                    <div class="input-group-append inputFieldHeight borderLeftNone">--}}
{{--                        <span class="input-group-text bg-white"><a href="javascript:void(0)"><i id="passwordEye"--}}
{{--                                    class="bi bi-eye-slash"></i></a></span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                @if ($errors->has('password'))--}}
{{--                    <span class="invalid-feedback" style="display: block;" role="alert">--}}
{{--                        <strong>{{ $errors->first('password') }}</strong>--}}
{{--                    </span>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--            <input type="submit" class="font12 btn btn-block btn-success btn-lg rounded greenButton" value="LOGIN">--}}
{{--        </form>--}}
{{--    </div>--}}
{{--    <div class="col-lg-12">--}}

{{--    </div>--}}
{{--@endsection--}}


{{--@section('script')--}}
{{--    <script src="{{ asset('js/authPage.js') }}"></script>--}}
{{--@endsection--}}
@extends('layouts.authMaster')

@section('title', 'Login')

@section('style')
    <link href="{{ asset('css/authPage.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="col-lg-12" style="margin-top:60%;">
        <h2 class="text-white mb-4">Sign in</h2>
        <p class="text-white" style="font-size: 14px;">Welcome to the RecruitmentAlly Dashboard.</p>
    </div>
    <div class="col-lg-12">
        @if (session()->has('message'))
            <div class="alert alert-danger">
                {{ session()->get('message') }}
            </div>
        @endif

        <form class="mt-4" action="{{ route('login') }}" method="post">
            @csrf
            <input type="hidden" name="time" id="timeZone">
            <div class="form-group">
                <label class="text-white" for="email"><small>Email address</small></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                    </div>
                    <input type="email" id="email" name="email" class="form-control" aria-describedby="emailHelp"
                           placeholder="Enter email">
                </div>
                @error('email')
                <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <label class="text-white" for="password"><small>Password</small></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
                    </div>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Password">
                    <div class="input-group-append">
                        <span class="input-group-text bg-white">
                            <a href="#" onclick="togglePasswordVisibility()">
                                <i id="passwordEye" class="bi bi-eye-slash"></i>
                            </a>
                        </span>
                    </div>
                </div>
                @error('password')
                <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-block btn-success btn-lg rounded-pill">LOGIN</button>
        </form>
    </div>

@endsection

@section('script')
    <script src="{{ asset('js/authPage.js') }}"></script>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("password");
            var passwordEye = document.getElementById("passwordEye");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                passwordEye.classList.remove("bi-eye-slash");
                passwordEye.classList.add("bi-eye");
            } else {
                passwordField.type = "password";
                passwordEye.classList.remove("bi-eye");
                passwordEye.classList.add("bi-eye-slash");
            }
        }
    </script>
@endsection
