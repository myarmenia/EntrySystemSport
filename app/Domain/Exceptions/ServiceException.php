<?php

namespace App\Domain\Exceptions;

use App\Domain\Errors\ErrorCode;

class ServiceException extends DomainException
{
    public static function attachPersonsFailed(\Throwable $e): self
    {
        return new self(ErrorCode::PERSON_ATTACH_FAILED, $e);
    }
}
