<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo/logo.png')}}">
    <title>@yield('title')</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <link href="{{ asset('css/phoneNumberInputField.css') }}" rel="stylesheet">
    <link href="{{ asset('css/authPage.css') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
    <div class="row imageBackground m-0">
        <div class="col-lg-4 logoPadding" data-aos="fade-right">
            <img src={{ asset('svg/mainLogo.svg') }} class="rounded mx-auto d-block" alt="..." width="250px" height="250px">
        </div>
        @yield('content')
    </div>
<script src="{{ asset('js/app.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<!-- InputMask -->
<script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js')}}"></script>
<!-- date-range-picker -->
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<script>
    //Date picker
    var maxBirthdayDate = new Date();
    maxBirthdayDate.setFullYear( maxBirthdayDate.getFullYear() - 18 );
    $('#dateOfBirthPicker').datetimepicker({
        format: 'L',
        maxDate: maxBirthdayDate
    });
    $('#postalCode').keyup( function() {
        let input = document.getElementById('postalCode').value;
        $("#error-postalcode").addClass("hide");
        if ( input.length > 0 && input.length < 5 ) {
            $("#error-postalcode").removeClass("hide");
        }
        if ( input.length > 5 && input.length < 10) {
            $("#error-postalcode").removeClass("hide");
        }
        if ( input.length > 5 ) {
            if ( input.length > 5 ) {
                $(this).val(input.replace(/(\d{5})(\d{1})/, "$1-$2"));
            }
            if ( input.length > 6 ) {
            $(this).val(input.replace(/(\d{5})(\d{2})/, "$1-$2"));
            }
            if ( input.length > 7 ) {
            $(this).val(input.replace(/(\d{5})(\d{3})/, "$1-$2"));
            }
            if ( input.length > 8 ) {
            $(this).val(input.replace(/(\d{5})(\d{4})/, "$1-$2"));
            }
        }
    });
    // newRegistrationUser
    $("#newRegistrationUser").submit(function (e) {
        var validationFailed = false;
        let input = document.getElementById('postalCode').value;
        if ( input.length > 0 && input.length < 5 ) {
            validationFailed = true;
        }
        if ( input.length > 5 && input.length < 10) {
            validationFailed = true;
        }
        if (validationFailed) {
            e.preventDefault();
            return false;
        }
    }); 

    AOS.init();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.min.js"></script>
<script src="{{ asset('js/register.js') }}"></script>
@yield('script')
</body>
</html>
