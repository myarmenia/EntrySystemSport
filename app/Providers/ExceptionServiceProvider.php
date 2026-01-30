<?php

namespace App\Providers;

use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\RepositoryException;
use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Illuminate\Support\ServiceProvider;
use Throwable;
use Illuminate\Validation\ValidationException;

class ExceptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->bind(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            function ($app) {
                return new class($app) extends BaseHandler {
                    public function render($request, Throwable $e)
                    {
                        if ($request->expectsJson()) {

                            // 1️⃣ Обработка ValidationException ПЕРВОЙ
                            if ($e instanceof ValidationException) {
                                return response()->json([
                                    'success' => false,
                                    'errors' => $e->errors(), // ошибки по полям
                                ], 422);
                            }

                            // 2️⃣ DomainException
                            if ($e instanceof DomainException) {
                                return response()->json([
                                    'success' => false,
                                    'error_code' => $e->errorCode->value,
                                    'message' => $e->errorCode->message(),
                                ], $e->errorCode->httpStatus());
                            }

                            // 3️⃣ RepositoryException (можно убрать, если наследует DomainException)
                            if ($e instanceof RepositoryException) {
                                return response()->json([
                                    'success' => false,
                                    'message' => $e->getMessage(),
                                ], $e->getCode() ?: 400);
                            }

                            // 4️⃣ Все остальные ошибки
                            return response()->json([
                                'success' => false,
                                'error_code' => 'UNKNOWN_ERROR',
                                'message' => 'Unexpected error occurred',
                            ], 500);
                        }

                        return parent::render($request, $e);
                    }
                };
            }
        );
    }
}
