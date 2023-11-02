<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {

        });
    }

    // protected function render($request, Exception $exception)
    // {
    //     if ($exception instanceof AuthenticationException) {
    //         if ($request->expectsJson()) {
    //             return $this->jsonErrorResponse('Unauthenticated.', 401);
    //         }
    //         // Handle other cases as needed
    //     }

    //     return parent::render($request, $exception);
    // }

    // protected function jsonErrorResponse($message, $statusCode)
    // {
    //     return new JsonResponse(['error' => $message], $statusCode);
    // }
}
