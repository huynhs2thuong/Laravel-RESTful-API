<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Exception\HttpResponseException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
            return response()->json([
                'status' => 'Failed',
                'message' => $exception->getMessage(),
                'error_code'=> "AUTH_MISSING_AUTHENTICATION_ERROR",
            ], $exception->getStatusCode());
        }
        if($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException){
            return response()->json([
                'status' => 'Failed',
                'message' => $exception->getMessage(),
                'error_code'=> "AUTH_TOKEN_MISSING_PREFIX",
                
            ], 401);
        }
        // if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
        //     return response()->json([
        //         'status' => 'Failed',
        //         'message' => $exception->getMessage()
        //     ], $exception->getStatusCode());
        // }
        if($exception instanceof \Illuminate\Database\QueryException){
            return response()->json([
                'status' => 'Failed',
                'message' => $exception->getMessage()
            ],422);
        }
        if($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException){
            return response()->json([
                'status' => 'Failed',
                'message' => \App\Exceptions\Exception::API_THROTTLE,
                'error_code' => "API_THROTTLE"
            ],429);
        }
        if($exception instanceof \ErrorException){
            return response()->json([
                'status' => 'Failed',
                'message' => $exception->getMessage(),
                'error_code' => "Object_Not_Found"
            ],500);
        }
        return parent::render($request, $exception);
    }
}
