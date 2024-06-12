<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class ValidateAPIKey
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (env('APP_SERVER_API_KEY') != $request->header('api-key')) {
            return response()->json(['response' => ['status' => false, 'message' => 'Sorry! Server could not recognize the Client. Either update your App or contact with admin']], JsonResponse::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
