<?php

namespace app\kernel\exception;

use app\kernel\traits\JsonResponse;
use Throwable;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends ExceptionHandler
{
    use JsonResponse;

    public $dontReport = [
        JsonErrorException::class,
    ];

    public function render(Request $request, Throwable $exception): Response
    {
        if ($exception instanceof JsonErrorException) {
            return $this->errorWithCode($exception->getCode(), $exception->getMessage());
        }

        return parent::render($request, $exception);
    }
}