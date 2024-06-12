<?php

namespace App\Exceptions;

use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait ExceptionTrait
{
    public function apiException($request, $e)
    {
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json(['response' => ['status' => false, 'message' => 'Invalid Request']], JsonResponse::HTTP_METHOD_NOT_ALLOWED);
        } elseif ($e instanceof ModelNotFoundException) {
            return response()->json(['response' => ['status' => false, 'message' => 'Requested information not found']], JsonResponse::HTTP_NOT_FOUND);
        } elseif ($e instanceof NotFoundHttpException) {
            return response()->json(['response' => ['status' => false, 'message' => 'Invalid URL Requested']], JsonResponse::HTTP_NOT_FOUND);
        }
        elseif ($e instanceof UnauthorizedHttpException) {
            return response()->json(['response' => ['status' => false, 'message' => 'Invalid Token']], JsonResponse::HTTP_UNAUTHORIZED);
        }
        elseif ($e instanceof AuthenticationException) {
            return response()->json(['response' => ['status' => false, 'message' => 'Unauthorized']], JsonResponse::HTTP_UNAUTHORIZED);
        }
        elseif ($e instanceof PDOException) {
            return response()->json(['response' => ['status' => false, 'message' => 'Invalid Request']], JsonResponse::HTTP_BAD_REQUEST);
        }
        return parent::render($request, $e);
    }
}
