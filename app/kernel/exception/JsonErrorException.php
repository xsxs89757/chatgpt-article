<?php

namespace app\kernel\exception;

use app\kernel\traits\JsonResponse;
use Exception;
use Throwable;

class JsonErrorException extends Exception
{
    use JsonResponse;

    public function __construct(string $message = '操作失败', int $code = 0, Throwable $previous = null)
    {
        if ($code == 0) $code = $this->CodeAdapter()::STATUS_ERROR;

        parent::__construct($message, $code, $previous);
    }
}