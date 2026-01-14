<?php

namespace App\Interfaces;

interface CheckEntryCodeInterface
{
    public function checkEntryCode($entry_code, $client_id);
}
