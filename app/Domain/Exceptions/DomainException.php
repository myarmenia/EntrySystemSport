<?php

namespace App\Domain\Exceptions;

use App\Domain\Errors\ErrorCode;
use RuntimeException;

abstract class DomainException extends RuntimeException
{
    public function __construct(
        public readonly ErrorCode $errorCode,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $errorCode->message(),
            $errorCode->httpStatus(),
            $previous
        );
    }
}
