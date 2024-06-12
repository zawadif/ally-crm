<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo/logo.png')}}">
    <title>{{ config('app.name', 'Tennis Fights') }} | @yield('title')</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/override.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <style>
        .userNameText{
            font-size: 12px !important;
            font-weight: 100 !important;
            color: white !important;
        }
    </style>
    @yield('style')
</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper" id="app">
    <!-- Header -->
@include('partialPages.header.webportHeader')
<!-- Sidebar -->
@include('partialPages.sidebar.webportSidebar')


@yield('content')
<!-- Footer -->
    {{-- @include('partialPages.footer.footer') --}}
</div>

<script src="{{asset('js/app.js')}}"></script>
<!-- InputMask -->
<script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
@toastr_render
@yield('script')

</body>
</html>
