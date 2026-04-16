<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e) {
            return response()->view('errors.404', [], 404);
        });

        $this->renderable(function (TokenMismatchException $e) {
            return response()->view('errors.419', [], 419);
        });

        $this->renderable(function (HttpExceptionInterface $e) {

            return match ($e->getStatusCode()) {
                403 => response()->view('errors.403', [], 403),
                404 => response()->view('errors.404', [], 404),
                default => null,
            };
        });

        $this->renderable(function (QueryException $e) {
            return response()->view('errors.500', [], 500);
        });

        $this->renderable(function (Throwable $e) {
            if (app()->environment('production')) {
                return response()->view('errors.500', [], 500);
            }
        });
    }
}