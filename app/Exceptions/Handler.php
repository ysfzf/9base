<?php

namespace App\Exceptions;

use App\Services\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {

        if($request->is("api/*")){
            try{
                $url=$request->url();
                $msg=$e->getMessage();
                $code=$e->getCode();

                if($e instanceof AuthenticationException||$e instanceof JWTException){
                    $code=10403;
                    $msg='AuthenticationException';
                }elseif($e instanceof NotFoundHttpException){
                    $msg='NotFoundHttpException';
                    $code=404;
                }elseif($e instanceof MethodNotAllowedHttpException){
                    $code=400;
                    $msg='MethodNotAllowedHttpException';
                }elseif($e instanceof ValidationException){
                    $code = 400;
                    $errors = array_values(collect($e->errors())->collapse()->toArray());
                    $msg = $errors ? $errors[0] : 'ValidationException';
                }elseif($e instanceof HttpException){
                    $code=$e->getStatusCode();
                }

                Log::error("【{$url}】{$code}:{$msg} {$e->getFile()} {$e->getLine()}");
                return Response::fail($msg?:'未知错误',null,$code?:500);
            }catch (\Throwable $e){
                Log::error("【Exception Handler】{$e->getMessage()} {$e->getFile()} {$e->getLine()}");
            }

        }
        return parent::render($request, $e);
    }
}
