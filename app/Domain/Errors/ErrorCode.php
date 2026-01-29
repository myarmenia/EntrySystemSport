<?php

namespace App\Domain\Errors;

enum ErrorCode: string
{
    case RECOMMENDATION_NOT_FOUND = 'RECOMMENDATION_NOT_FOUND';
    case PERSON_SYNC_FAILED = 'PERSON_SYNC_FAILED';
    case PERSON_ATTACH_FAILED = 'PERSON_ATTACH_FAILED';
    case UNKNOWN_ERROR = 'UNKNOWN_ERROR';
    case PERSON_ALREADY_EXISTS = 'PERSON_ALREADY_EXISTS';

    public function httpStatus(): int
    {
        return match ($this) {
            self::RECOMMENDATION_NOT_FOUND => 404,
            self::PERSON_SYNC_FAILED,
            self::PERSON_ATTACH_FAILED => 422,
            self::UNKNOWN_ERROR => 500,
            self::PERSON_ALREADY_EXISTS => 409,
        };
    }

    public function message(): string
    {
        return match ($this) {
        self::RECOMMENDATION_NOT_FOUND => 'Համապատասխան խորհուրդը չի գտնվել',
        self::PERSON_SYNC_FAILED => 'Մարզվողներին սինքրոնացնելը ձախողվեց',
        self::PERSON_ATTACH_FAILED => 'Մարզվողներին կցելը ձախողվեց',
        self::UNKNOWN_ERROR => 'Անսպասելի սխալ է տեղի ունեցել',
        self::PERSON_ALREADY_EXISTS => 'Մարզչի խորհուրդը արդեն կցված է',
    };
    }
}
