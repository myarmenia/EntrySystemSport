<?php

namespace App\Repositories;
use App\Interfaces\CreateEntryCodeInterface;
use App\Models\EntryCode;


class EntryCodeRepository implements CreateEntryCodeInterface
{

    public function create(array $data): EntryCode
    {
        return EntryCode::create($data);
    }


}
