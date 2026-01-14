<?php

namespace App\Interfaces;

interface ClientIdFromTurnstileInterface
{
    public function getClientId(array $mac);
}
