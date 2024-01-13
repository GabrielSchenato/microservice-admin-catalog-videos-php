<?php

namespace App\Exceptions;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Notification\NotificationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundException)
            return $this->showError($e->getMessage(), Response::HTTP_NOT_FOUND);

        if ($e instanceof EntityValidationException)
            return $this->showError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        if ($e instanceof NotificationException)
            return $this->showError($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        return parent::render($request, $e);
    }

    private function showError(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message
        ], $statusCode);
    }
}
