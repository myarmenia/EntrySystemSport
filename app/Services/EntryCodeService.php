<?php

namespace App\Services;


use App\DTO\EntryCodeDto;
use App\Interfaces\ClientIdFromTurnstileInterface;
use App\Interfaces\CreateEntryCodeInterface;
use Illuminate\Http\Request;

class EntryCodeService
{

    public function __construct(
        protected CreateEntryCodeInterface $createEntryCodeRepository,
        protected ClientIdFromTurnstileInterface $clientIdFromTurnstileRepository

    ) {
    }


    public function store(EntryCodeDto $dto)
    {

        $result = null;
        $message = 'success';

        $client_id = $this->clientIdFromTurnstileRepository->getClientId($dto->mac);

        if(!$client_id){
            $message = 'invalid mac';
        }

        else{

            $dto->client_id = $client_id;
            $result = $this->createEntryCodeRepository->create($dto->toArray());
        }

        return (object) [
            'message' => $message,
            'result' => $result
        ];

    }



}
