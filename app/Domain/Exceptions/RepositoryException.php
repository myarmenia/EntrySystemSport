<?php

namespace App\Domain\Exceptions;

use App\Domain\Errors\ErrorCode;

class RepositoryException extends DomainException
{
    public static function recommendationNotFound(\Throwable $e): self
    {
        return new self(ErrorCode::RECOMMENDATION_NOT_FOUND, $e);
    }

    public static function syncFailed(\Throwable $e): self
    {
        return new self(ErrorCode::PERSON_SYNC_FAILED, $e);
    }
    public static function alreadyExists(): self
    {
        return new self(ErrorCode::PERSON_ALREADY_EXISTS);
    }
}
