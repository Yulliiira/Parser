<?php

namespace App\Services\LogParser;

use App\Contracts\LogParser\PatternInterface;

class Pattern implements PatternInterface
{
    public function getIdentifiers(): array
    {
        return ['ip_address', 'request_date', 'url', 'user_agent'];
    }
}