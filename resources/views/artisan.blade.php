<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <link rel="icon" href="{{ asset('img/logo/logo.png') }}" type="image/png">

    <title>Artisan Commands</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
   
</head>

<body class="hold-transition sidebar-mini">

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session()->get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <a href="{{url('artisan-logout')}}" class="btn btn-primary btn-sm pull-right">Logout</a>
    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
           

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-2  ">

                <div class="max-w-md py-4 px-8 bg-white shadow-lg rounded-lg mt-20 grid-cols-1 md:grid-cols-2">
                    <div>
                        <h4 class="text-gray-600 text-2xl font-semibold">Run Optimize</h4>
                        <p class="mt-2 text-gray-600">  Remove the cached bootstrap files.</p>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a href="{{url('php_artisan_cmd')."?key=optimize"}}" class="buttonClickOverlay text-l font-small text-indigo-500">Run Command</a>
                    </div>
                </div>
                
                {{-- ml-3 --}}
                <div class="max-w-md py-4 px-8 bg-white shadow-lg ml-3 rounded-lg mt-20 grid-cols-1 md:grid-cols-2">
                    <div>
                        <h4 class="text-gray-600 text-2xl font-semibold">Run Migrations & Seed Data</h4>
                        <p class="mt-2 text-gray-600">The migrate:fresh command will drop all tables from the database and then execute the migrate command.</p>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a href="{{url('php_artisan_cmd')."?key=db_dump"}}" class="buttonClickOverlay text-l font-small text-indigo-500">Run Command</a>
                    </div>
                </div>

                <div class="max-w-md py-4 px-8 bg-white shadow-lg rounded-lg mt-20 grid-cols-1 md:grid-cols-2">
                    <div>
                        <h4 class="text-gray-600 text-2xl font-semibold">Run Migrations</h4>
                        <p class="mt-2 text-gray-600">To run all of your outstanding migrations, execute the migrate Artisan command.</p>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a href="{{url('php_artisan_cmd')."?key=migrate"}}" class="buttonClickOverlay text-l font-small text-indigo-500">Run Command</a>
                    </div>
                </div>

                {{-- ml-3 --}}
                <div class="max-w-md py-4 px-8 bg-white shadow-lg ml-3 rounded-lg mt-20 grid-cols-1 md:grid-cols-2">
                    <div>
                        <h4 class="text-gray-600 text-2xl font-semibold">Run Migrations Fresh</h4>
                        <p class="mt-2 text-gray-600">The migrate:fresh command will drop all tables from the database and then execute the migrate command.</p>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a href="{{url('php_artisan_cmd')."?key=fresh"}}" class="buttonClickOverlay text-l font-small text-indigo-500">Run Command</a>
                    </div>
                </div>

                <div class="max-w-md py-4 px-8 bg-white shadow-lg rounded-lg mt-20 grid-cols-1 md:grid-cols-2">
                    <div>
                        <h4 class="text-gray-600 text-2xl font-semibold">Run Seed Data</h4>
                        <p class="mt-2 text-gray-600">You may execute the db:seed Artisan command to seed your database.</p>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a href="{{url('php_artisan_cmd')."?key=seed"}}" class="buttonClickOverlay text-l font-small text-indigo-500">Run Command</a>
                    </div>
                </div>

                {{-- ml-3 --}}
                <div class="max-w-md py-4 px-8 bg-white shadow-lg rounded-lg ml-3 mt-20 grid-cols-1 md:grid-cols-2">
                    <div>
                        <h4 class="text-gray-600 text-2xl font-semibold">Run Cache Clear</h4>
                        <p class="mt-2 text-gray-600">Flush the application cache.</p>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a href="{{url('php_artisan_cmd')."?key=cache"}}" class="buttonClickOverlay text-l font-small text-indigo-500">Run Command</a>
                    </div>
                </div>

                <div class="max-w-md py-4 px-8 bg-white shadow-lg rounded-lg mt-20 grid-cols-1 md:grid-cols-2">
                    <div>
                        <h4 class="text-gray-600 text-2xl font-semibold">Run Config Clear</h4>
                        <p class="mt-2 text-gray-600"> Remove the configuration cache file.</p>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a href="{{url('php_artisan_cmd')."?key=config"}}" class="buttonClickOverlay text-l font-small text-indigo-500">Run Command</a>
                    </div>
                </div>


            </div>
        </div>
    </div>

<script src="{{asset('js/app.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>

<script>
    // $.LoadingOverlay('show');
    $.LoadingOverlay('hide');

    $(document).on('click', '.buttonClickOverlay', function() {
        $.LoadingOverlay('show');
    });


    @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            toastr.error("{{ $error }}");
        @endforeach
    @endif
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    var baseUrl="{{ url('/') }}"
</script>
@toastr_render


</body>
</html>
