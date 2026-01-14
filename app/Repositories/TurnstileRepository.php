<?php

namespace App\Repositories;
use App\Interfaces\CheckEntryCodeInterface;
use App\Interfaces\ClientIdFromTurnstileInterface;
use App\Models\EntryCode;
use App\Models\Turnstile;


class TurnstileRepository implements ClientIdFromTurnstileInterface, CheckEntryCodeInterface
{

    public function getClientId($mac):mixed
    {
        $turnstile = Turnstile::where('mac', $mac)->first();
        return $turnstile != null ? $turnstile->client_id : false;
    }

    public function checkEntryCode($entry_code, $client_id):object
    {
        $message = 'success';
        $result = false;

        $entry_code = EntryCode::where([
            'token' => $entry_code,
            'client_id' => $client_id
        ])->first();

        if (!$entry_code) {
             $message = 'Code not found.';

        } elseif ($entry_code->status != 1) {
             $message = 'The code is not active.';

        } elseif ($entry_code->activation != 1) {
             $message = 'The code was not activated.';

        }
        else{
            $result = $entry_code != null ? $entry_code : false;

        }

        return (object) [
            'message' => $message,
            'result' => $result
        ];
    }
}
