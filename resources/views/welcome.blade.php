<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Welcome</title>
       <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-700 underline">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
                   <img src="{{asset('img/logo/logo.png')}}" style="width: 30%">
                </div>


                    <div class="grid grid-cols-2  ">
                        <div class="max-w-md py-4 px-8 bg-white shadow-lg rounded-lg my-20 grid-cols-1 md:grid-cols-2">
                         <div>
                            <h2 class="text-gray-800 text-3xl font-semibold">Authentication Web</h2>
                            <p class="mt-2 text-gray-600">Laravel fortify is used to handle all auth related operations
                            , please make sure to visit official documentation before starting the work.</p>
                          </div>
                          <div class="flex justify-end mt-4">
                            <a href="https://laravel.com/docs/8.x/fortify" class="text-xl font-medium text-indigo-500">Visit Docs</a>
                          </div>
                        </div>
                         <div class="max-w-md ml-10 py-2 px-8 bg-white shadow-lg rounded-lg my-20 grid-cols-1 md:grid-cols-2">
                         <div>
                            <h2 class="text-gray-800 text-3xl font-semibold">Authentication Api</h2>
                            <p class="mt-2 text-gray-600">Laravel Sanctum is used to handle all api auth related operations
                            , please make sure to visit official documentation before starting the work.</p>
                          </div>
                          <div class="flex justify-end mt-4">
                            <a href="https://laravel.com/docs/8.x/sanctum#introduction" class="text-xl font-medium text-indigo-500">Visit Docs</a>
                          </div>
                        </div>
                      </div>

                    
                      
                     

                      <div class="grid grid-row">
                        <div>
                        <h2 class="text-gray-800 text-3xl mb-4 font-bold">Other Common Dependencies</h2>
                        </div> 
                        <div class="bg-white rounded  py-4 px-8 bg-white shadow-lg rounded-lg ">
                        <ul class="list-disc">
                          <li class="text-2xl"><code class="text-blue-800">bensampo/laravel-enum</code> 
                          to handle all enums. </li>
                          <li class="text-2xl"><code class="text-blue-800">laravel/telescope</code> 
                          to track application performace and debbug. </li>
                           <li class="text-2xl"><code class="text-blue-800">league/flysystem-aws-s3-v3</code> 
                          to handle s3 operations. </li>
                           <li class="text-2xl"><code class="text-blue-800">spatie/laravel-permission</code> 
                          to handle all roles related operation. </li>
                          <li class="text-2xl"><code class="text-blue-800">twilio/sdk</code> 
                          to handle all phone verification related operation. </li>
                          <li class="text-2xl"><code class="text-blue-800">vlucas/phpdotenv</code> 
                          to handle all env related operation. </li>
                          <li class="text-2xl"><code class="text-blue-800">yajra/laravel-datatables-oracle</code> 
                          to handle all view data related operation. </li>
                          <li class="text-2xl"><code class="text-blue-800">yoeunes/toastr</code> 
                          to handle all alert related operation. </li>
                          <li class="text-2xl"><code class="text-blue-800">kreait/laravel-firebase</code> 
                          to handle all firebase related operation. </li>
                          <li class="text-2xl"><code class="text-blue-800">facebook/graph-sdk</code> 
                          to handle facebook related operation. </li>
                        </ul> 
                      </div>
                      </div>





                <div class="flex justify-center mt-4 sm:items-center sm:justify-between">

                    <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
