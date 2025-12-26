<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\RequestMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'jwt' => JwtMiddleware::class,
        ]);

        $middleware->append(RequestMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return true; // Always render JSON
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            $statusCode = $e->getCode();
            $statusCode = is_numeric($statusCode) && $statusCode >= 100 && $statusCode < 600
                ? $statusCode
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            if (app()->environment(['local', 'development', 'testing'])) {
                $response['trace'] = $e->getTrace();
            }
            return response()->json($response, $statusCode);
        });
    })->create();
