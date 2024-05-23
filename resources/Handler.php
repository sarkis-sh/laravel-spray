<?php

namespace App\Exceptions;

use App\Traits\FileTrait;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use FileTrait, ResponseTrait;
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
        $msg  =  $e->getMessage();
        $code =  $e->getCode();
        $errorList = [];

        switch (true) {
            case $e instanceof ValidationException:
                $msg = $e->validator->errors()->first();
                $errorList = $e->validator->errors();
                $code = 400;
                break;
            case $e instanceof NotFoundHttpException:
                $msg = __('messages.routeNotFound');
                $code = 404;
                break;
            case $e instanceof AuthenticationException:
                $msg = __('auth.unauthenticated');
                $code = 401;
                break;
        }

        if ($request->uploadedFiles != null) {
            foreach ($request->uploadedFiles as $uploadedFile) {
                $this->deleteFile($uploadedFile);
            }
        }

        if (!$code || $code > 599 || $code <= 0 || gettype($code) !== "integer") {
            $code = 500;
        }

        return $this->errorResponse($msg, $code, $errorList);
    }
}
